<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class AnnotatorManager extends Component
{
    public Project $project;

    public string $searchQuery = '';
    public array  $searchResults = [];
    public bool   $searching = false;

    public function updatedSearchQuery(): void
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = User::where('id', '!=', auth()->id())
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
            })
            ->whereDoesntHave('projectUsers', fn($q) =>
                $q->where('project_id', $this->project->id)
            )
            ->limit(8)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }



    public function render(): View
    {
        $annotators = $this->project->annotators()
            ->withCount([
                'annotations as annotation_count' => fn($q) =>
                    $q->where('project_id', $this->project->id),
            ])
            ->get();

        return view('livewire.projects.annotator-manager', compact('annotators'));
    }
}
