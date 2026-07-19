<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        // Check if the route parameter is a Project model instance
        if ($project instanceof \App\Models\Project) {
            if ($project->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action. Only the project owner can perform this action.');
            }
        } 
        // Fallback in case it's an ID
        elseif (is_numeric($project)) {
            $projectModel = \App\Models\Project::find($project);
            if ($projectModel && $projectModel->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action. Only the project owner can perform this action.');
            }
        }

        return $next($request);
    }
}
