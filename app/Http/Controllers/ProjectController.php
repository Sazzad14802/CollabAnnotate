<?php

namespace App\Http\Controllers;

use App\Jobs\ImportProjectJob;
use App\Models\AnnotationField;
use App\Models\Project;
use App\Services\ProjectImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(): View
    {
        $ownedProjects = auth()->user()->ownedProjects()
            ->with('members')
            ->withCount('annotators')
            ->latest()
            ->get();

        return view('projects.index', compact('ownedProjects'));
    }

    public function assigned(): View
    {
        return view('projects.assigned');
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request, ProjectImportService $importService): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn ($query) => $query->where('user_id', $user->id))
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'file'        => ['required', 'file', 'extensions:csv,xlsx', 'max:51200'],
            'chunk_size'  => ['nullable', 'integer', 'min:1', 'max:1000'],
            'schema'      => ['required', 'array', 'min:1'],
            'schema.*.name'    => ['required', 'string', 'max:100'],
            'schema.*.type'    => ['required', 'in:select,checkbox'],
            'schema.*.options' => ['nullable', 'string', 'required_if:schema.*.type,select'],
            'schema.*.is_required' => ['boolean'],
        ]);

        // Create the project first (without file info — import service fills that)
        $project = Project::create([
            'user_id'     => $user->id,
            'name'        => $request->name,
            'description' => $request->description,
            'chunk_size'  => $request->chunk_size ?? 10,
            'import_status' => 'pending',
        ]);

        // Add owner to project_users
        $project->members()->attach($user->id, [
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        // Save schema fields
        foreach ($request->schema as $index => $fieldData) {
            $options = null;
            if ($fieldData['type'] === 'select' && !empty($fieldData['options'])) {
                $options = array_values(array_filter(array_map('trim', explode(',', $fieldData['options']))));
            }

            AnnotationField::create([
                'project_id'  => $project->id,
                'name'        => $fieldData['name'],
                'slug'        => str($fieldData['name'])->slug()->toString(),
                'type'        => $fieldData['type'],
                'options'     => $options,
                'is_required' => !empty($fieldData['is_required']),
                'order'       => $index,
            ]);
        }

        // Store the file and kick off background import
        $importService->prepareProject($request->file('file'), $project);
        ImportProjectJob::dispatch($project, $user);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created! Your CSV is being imported in the background.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['annotators', 'annotationFields']);

        return view('projects.show', compact('project'));
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
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn ($query) => $query->where('user_id', auth()->id()))->ignore($project->id)
            ],
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

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
