<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class AnnotatedRows extends Component
{
    use WithPagination;

    public Project $project;

    public function render()
    {
        $completedAssignments = $this->project->rowAssignments()
            ->where('status', 'completed')
            ->with(['datasetRow.annotations' => function ($query) {
                $query->where('project_id', $this->project->id);
            }, 'user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('livewire.projects.annotated-rows', [
            'completedAssignments' => $completedAssignments,
        ]);
    }
}
