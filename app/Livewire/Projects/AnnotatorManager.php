<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectInvited;
use App\Notifications\ProjectRemoved;
use App\Services\ActivityLogService;
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

    public function addAnnotator(int $userId): void
    {
        $this->authorize('manageAnnotators', $this->project);

        $user = User::findOrFail($userId);

        $this->project->members()->syncWithoutDetaching([
            $user->id => ['role' => 'annotator', 'joined_at' => now()],
        ]);

        ActivityLogService::log(auth()->user(), 'annotator.added',
            "Annotator \"{$user->name}\" added to project.", $this->project);

        // $user->notify(new ProjectInvited($this->project));

        $this->searchQuery   = '';
        $this->searchResults = [];
        $this->dispatch('annotator-added');
    }

    public function removeAnnotator(int $userId): void
    {
        $this->authorize('manageAnnotators', $this->project);

        $user = User::findOrFail($userId);

        if ($this->project->user_id === $userId) {
            $this->addError('remove', 'Cannot remove the project owner.');
            return;
        }

        $this->project->members()->detach($userId);

        ActivityLogService::log(auth()->user(), 'annotator.removed',
            "Annotator \"{$user->name}\" removed from project.", $this->project);

        // $user->notify(new ProjectRemoved($this->project));
        $this->dispatch('annotator-removed');
    }

    public function render(): View
    {
        $annotators = $this->project->annotators()
            // ->withCount([
            //     'annotations as annotation_count' => fn($q) =>
            //         $q->where('project_id', $this->project->id),
            // ])
            ->get();
            
        foreach ($annotators as $annotator) {
            $annotator->annotation_count = 0;
        }

        return view('livewire.projects.annotator-manager', compact('annotators'));
    }
}
