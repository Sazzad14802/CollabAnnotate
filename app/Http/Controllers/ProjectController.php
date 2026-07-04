<?php

namespace App\Http\Controllers;

use App\Models\AnnotationField;
use App\Models\Dataset;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

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

    public function assigned(): View
    {
        return view('projects.assigned');
    }

    public function create(): View
    {
        $datasets = auth()->user()->datasets()
            ->where('import_status', 'completed')
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('datasets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn ($query) => $query->where('user_id', $user->id))
            ],
            'description'       => ['nullable', 'string', 'max:2000'],
            'dataset_id'        => ['required', 'exists:datasets,id'],
            'chunk_size'        => ['nullable', 'integer', 'min:1', 'max:1000'],
            'schema'            => ['required', 'array', 'min:1'],
            'schema.*.name'     => ['required', 'string', 'max:100'],
            'schema.*.type'     => ['required', 'in:select,checkbox'],
            'schema.*.options'  => ['nullable', 'string', 'required_if:schema.*.type,select'],
            'schema.*.is_required' => ['boolean'],
        ]);

        $dataset = Dataset::findOrFail($request->dataset_id);
        $this->authorize('view', $dataset);

        $project = Project::create([
            'user_id'     => $user->id,
            'dataset_id'  => $dataset->id,
            'name'        => $request->name,
            'description' => $request->description,
            'chunk_size'  => $request->chunk_size ?? 10,
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

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['dataset', 'annotators', 'annotationFields']);

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
