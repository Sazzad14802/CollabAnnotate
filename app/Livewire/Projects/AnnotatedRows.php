<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class AnnotatedRows extends Component
{

    public Project $project;
    public string $annotatorEmail = '';

    public function render()
    {
        $query = \App\Models\DatasetRow::where('project_id', $this->project->id)
            ->orderBy('row_index');

        if (!empty($this->annotatorEmail)) {
            $email = $this->annotatorEmail;
            $query->whereHas('annotations.user', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
            $query->with(['annotations' => function($q) use ($email) {
                $q->whereHas('user', function ($q2) use ($email) {
                    $q2->where('email', 'like', '%' . $email . '%');
                });
            }, 'annotations.user']);
        } else {
            $query->with(['annotations.user', 'rowAssignments.user']);
        }

        $datasetRows = $query->get();

        return view('livewire.projects.annotated-rows', [
            'datasetRows' => $datasetRows,
        ]);
    }
}
