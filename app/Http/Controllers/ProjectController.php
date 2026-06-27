<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDatasetJob;
use App\Models\Dataset;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\DatasetImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $ownedProjects = auth()->user()->ownedProjects()
            ->with(['dataset', 'members'])
            ->withCount('annotators')
            ->latest()
            ->get();
            
        foreach ($ownedProjects as $p) {
            // $p->annotators_count = 0;
        }

        return view('projects.index', compact('ownedProjects'));
    }

    public function create(): View
    {
        $datasets = auth()->user()->datasets()
            ->where('import_status', 'completed')
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('datasets'));
    }

    public function store(Request $request, DatasetImportService $importService): RedirectResponse
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'dataset_source'    => ['required', 'in:upload,existing'],
            'dataset_id'        => ['required_if:dataset_source,existing', 'exists:datasets,id'],
            'dataset_file'      => ['required_if:dataset_source,upload', 'file', 'mimes:csv,xlsx', 'max:51200'],
            'dataset_name'      => ['required_if:dataset_source,upload', 'nullable', 'string', 'max:255'],
            'chunk_size'        => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $user = auth()->user();

        // Handle dataset
        if ($request->dataset_source === 'upload') {
            $dataset = $importService->prepareDataset(
                $request->file('dataset_file'),
                $request->dataset_name,
                $user->id
            );

            ActivityLogService::log($user, 'dataset.uploaded',
                "Dataset \"{$dataset->name}\" uploaded.");
        } else {
            $dataset = Dataset::findOrFail($request->dataset_id);
            $this->authorize('view', $dataset);
        }

        $project = Project::create([
            'user_id'     => $user->id,
            'dataset_id'  => $dataset->id,
            'name'        => $request->name,
            'description' => $request->description,
            'chunk_size'  => $request->chunk_size ?? 50,
        ]);

        // Add owner to project_users
        $project->members()->attach($user->id, [
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        ActivityLogService::log($user, 'project.created',
            "Project \"{$project->name}\" created.", $project);

        // Dispatch import job if new upload
        if ($request->dataset_source === 'upload') {
            ImportDatasetJob::dispatch($dataset, $user);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['dataset', 'annotators']);
        // $project->load(['annotationFields']);

        $recentActivity = collect([]);
        // $recentActivity = $project->activityLogs()
        //     ->with('user')
        //     ->latest()
        //     ->limit(10)
        //     ->get();

        return view('projects.show', compact('project', 'recentActivity'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'chunk_size'  => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status'      => ['nullable', 'in:active,completed,archived'],
        ]);

        $project->update($request->only('name', 'description', 'chunk_size', 'status'));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        ActivityLogService::log(auth()->user(), 'project.deleted',
            "Project \"{$project->name}\" deleted.");

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
