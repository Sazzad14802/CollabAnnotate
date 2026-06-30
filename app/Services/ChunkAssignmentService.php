<?php

namespace App\Services;

use App\Models\DatasetRow;
use App\Models\Project;
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
        // First check if user already has in_progress rows
        $existing = DatasetRow::where('dataset_id', $project->dataset_id)
            ->where('assigned_to', $user->id)
            ->where('status', 'in_progress')
            ->get();

        if ($existing->isNotEmpty()) {
            return $existing;
        }

        return DB::transaction(function () use ($project, $user) {
            // Lock and fetch unassigned rows
            $rows = DatasetRow::where('dataset_id', $project->dataset_id)
                ->where('status', 'unassigned')
                ->orderBy('row_index')
                ->limit($project->chunk_size)
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) {
                return collect();
            }

            $ids = $rows->pluck('id');

            DatasetRow::whereIn('id', $ids)->update([
                'assigned_to' => $user->id,
                'status'      => 'in_progress',
            ]);

            return $rows->fresh();
        });
    }

    /**
     * Mark a row as completed when all required fields are annotated.
     */
    public function markRowCompleted(DatasetRow $row, Project $project): void
    {
        $totalFields     = $project->annotationFields()->where('is_required', true)->count();
        $annotatedFields = $row->annotations()
            ->where('project_id', $project->id)
            ->where('user_id', $row->assigned_to)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->count();

        // If no required fields, mark complete when any annotation is saved
        $shouldComplete = $totalFields === 0
            ? $row->annotations()->where('project_id', $project->id)->exists()
            : $annotatedFields >= $totalFields;

        if ($shouldComplete && $row->status !== 'completed') {
            $row->update(['status' => 'completed']);
        }
    }
}
