<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /** Owner or assigned annotator can view the project */
    public function view(User $user, Project $project): bool
    {
        return $project->user_id === $user->id
            || $project->members()->where('user_id', $user->id)->exists();
    }

    /** Only owner can update project settings */
    public function update(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    /** Only owner can delete the project */
    public function delete(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    /** Owner or annotators can annotate */
    public function annotate(User $user, Project $project): bool
    {
        return $project->user_id === $user->id
            || $project->members()->where('user_id', $user->id)->exists();
    }

    /** Only owner can manage annotators */
    public function manageAnnotators(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    /** Only owner can export */
    public function export(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    /** Only owner can manage annotation schema */
    public function manageSchema(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}
