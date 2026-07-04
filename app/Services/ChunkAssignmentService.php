<?php

namespace App\Services;

use App\Models\DatasetRow;
use App\Models\Project;
use App\Models\RowAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChunkAssignmentService
{
    /**
     * Assign the next chunk of unassigned rows to the given user.
     * Returns the assigned rows.
     */
    public function assignChunk(Project $project, User $user): Collection
    {
        // First check if user already has in_progress rows for this project
        $existingAssignments = RowAssignment::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->pluck('dataset_row_id');

        if ($existingAssignments->isNotEmpty()) {
            return DatasetRow::whereIn('id', $existingAssignments)->get();
        }

        return DB::transaction(function () use ($project, $user) {
            // Find rows in this dataset that are NOT assigned in this project
            $rows = DatasetRow::where('dataset_id', $project->dataset_id)
                ->whereNotIn('id', function($query) use ($project) {
                    $query->select('dataset_row_id')
                          ->from('row_assignments')
                          ->where('project_id', $project->id);
                })
                ->orderBy('row_index')
                ->limit($project->chunk_size)
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) {
                return collect();
            }

            // Create assignments
            $assignments = [];
            $now = now();
            foreach ($rows as $row) {
                $assignments[] = [
                    'project_id'     => $project->id,
                    'dataset_row_id' => $row->id,
                    'user_id'        => $user->id,
                    'status'         => 'in_progress',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
            
            RowAssignment::insert($assignments);

            return $rows;
        });
    }

    /**
     * Mark a row as completed when all required fields are annotated.
     */
    public function markRowCompleted(DatasetRow $row, Project $project): void
    {
        $assignment = RowAssignment::where('project_id', $project->id)
            ->where('dataset_row_id', $row->id)
            ->first();

        if (!$assignment) return;

        $totalFields     = $project->annotationFields()->where('is_required', true)->count();
        $annotatedFields = $row->annotations()
            ->where('project_id', $project->id)
            ->where('user_id', $assignment->user_id)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->count();

        // If no required fields, mark complete when any annotation is saved
        $shouldComplete = $totalFields === 0
            ? $row->annotations()->where('project_id', $project->id)->exists()
            : $annotatedFields >= $totalFields;

        if ($shouldComplete && $assignment->status !== 'completed') {
            $assignment->update(['status' => 'completed']);
        }
    }
}
