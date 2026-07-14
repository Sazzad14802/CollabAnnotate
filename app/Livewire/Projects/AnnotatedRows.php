<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class AnnotatedRows extends Component
{

    public Project $project;

    public function render()
    {
        $datasetRows = \App\Models\DatasetRow::where('project_id', $this->project->id)
            ->with(['annotations.user', 'rowAssignments.user'])
            ->orderBy('row_index')
            ->get();

        return view('livewire.projects.annotated-rows', [
            'datasetRows' => $datasetRows,
        ]);
    }
}
