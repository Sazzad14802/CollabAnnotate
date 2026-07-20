<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;

class ProgressTracker extends Component
{
    public Project $project;

    #[On('progress-updated')]
    public function updateStats()
    {
        // This blank method triggers a component re-render when the event is caught
    }

    public function render(): View
    {
        $total     = $this->project->row_count;
        $completed = $this->project->rowAssignments()->where('status', 'completed')->count();
        $remaining = $total - $completed;
        $percent   = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        // Combine annotators and the project owner for statistics tracking
        $usersToTrack = $this->project->annotators->push($this->project->owner)->unique('id');

        $annotatorStats = $usersToTrack
            ->map(function ($annotator) {
                $annotated = $this->project->rowAssignments()
                    ->where('user_id', $annotator->id)
                    ->where('status', 'completed')
                    ->count();

                $lastActivity = $annotator->annotations()
                    ->where('project_id', $this->project->id)
                    ->latest('updated_at')
                    ->value('updated_at');

                $assignedCount = $this->project->rowAssignments()
                    ->where('user_id', $annotator->id)
                    ->count();

                return [
                    'id'            => $annotator->id,
                    'name'          => $annotator->name,
                    'email'         => $annotator->email,
                    'is_owner'      => $annotator->id === $this->project->user_id,
                    'annotated'     => $annotated,
                    'assigned'      => $assignedCount,
                    'percent'       => $assignedCount > 0
                        ? round(($annotated / $assignedCount) * 100, 1)
                        : 0,
                    'last_activity' => $lastActivity,
                ];
            })
            ->sortByDesc('annotated')
            ->values();

        return view('livewire.projects.progress-tracker', compact(
            'total', 'completed', 'remaining', 'percent', 'annotatorStats'
        ));
    }
}
