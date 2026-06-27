<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class AssignedProjects extends Component
{
    public function render(): View
    {
        $projects = auth()->user()->assignedProjects()
            ->with(['owner', 'dataset'])
            ->latest('project_users.joined_at')
            ->get();

        return view('livewire.dashboard.assigned-projects', compact('projects'));
    }
}
