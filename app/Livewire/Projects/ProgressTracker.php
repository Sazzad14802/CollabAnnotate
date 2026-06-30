<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ProgressTracker extends Component
{
    public Project $project;

    public function render(): View
    {
        // Eager load needed relations
        $this->project->loadMissing(['dataset', 'annotators']);

        $dataset   = $this->project->dataset;
        $total     = $dataset->row_count;
        $completed = $dataset->rows()->where('status', 'completed')->count();
        $remaining = $total - $completed;
        $percent   = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        // Per-annotator stats
        $annotatorStats = $this->project->annotators
            ->map(function ($annotator) {
                $annotated = $annotator->annotations()
                    ->where('project_id', $this->project->id)
                    ->distinct('dataset_row_id')
                    ->count('dataset_row_id');

                $lastActivity = $annotator->annotations()
                    ->where('project_id', $this->project->id)
                    ->latest('updated_at')
                    ->value('updated_at');

                $assignedCount = $this->project->dataset->rows()
                    ->where('assigned_to', $annotator->id)
                    ->count();

                return [
                    'id'            => $annotator->id,
                    'name'          => $annotator->name,
                    'annotated'     => $annotated,
                    'assigned'      => $assignedCount,
                    'percent'       => $assignedCount > 0
                        ? round(($annotated / $assignedCount) * 100, 1)
                        : 0,
                    'last_activity' => $lastActivity,
                ];
            });

        return view('livewire.projects.progress-tracker', compact(
            'total', 'completed', 'remaining', 'percent', 'annotatorStats'
        ));
    }
}
