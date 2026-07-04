<?php

namespace App\Livewire\Projects;

use App\Models\Annotation;
use App\Models\AnnotationField;
use App\Models\DatasetRow;
use App\Models\Project;
use App\Services\ChunkAssignmentService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class AnnotationWorkspace extends Component
{
    use WithPagination;

    public Project $project;

    // Annotation data keyed by [row_id][field_id]
    public array $annotations = [];

    // Which row is "active" (focused for keyboard nav)
    public ?int $activeRowId = null;

    // Tracks saved state flash per row
    public array $savedRows = [];

    protected ChunkAssignmentService $chunkService;

    public function boot(ChunkAssignmentService $chunkService): void
    {
        $this->chunkService = $chunkService;
    }

    public function mount(): void
    {
        $this->authorize('annotate', $this->project);

        // Pre-load existing annotations for current user into state
        $this->loadAnnotations();
    }

    public function fetchNext(): void
    {
        $this->authorize('annotate', $this->project);
        
        // Assign a chunk to the current user
        $this->chunkService->assignChunk($this->project, auth()->user());
        
        // Reload annotations
        $this->loadAnnotations();
    }



    private function loadAnnotations(): void
    {
        $existing = Annotation::where('project_id', $this->project->id)
            ->where('user_id', auth()->id())
            ->get();

        foreach ($existing as $ann) {
            $this->annotations[$ann->dataset_row_id][$ann->annotation_field_id] = $ann->value;
        }
    }

    /**
     * Live-save a single annotation value.
     */
    public function saveAnnotation(int $rowId, int $fieldId, mixed $value): void
    {
        $this->authorize('annotate', $this->project);

        // Verify the row belongs to this project's dataset
        $row = DatasetRow::where('id', $rowId)
            ->where('dataset_id', $this->project->dataset_id)
            ->firstOrFail();

        $field = AnnotationField::where('id', $fieldId)
            ->where('project_id', $this->project->id)
            ->firstOrFail();

        // Upsert annotation
        Annotation::updateOrCreate(
            [
                'project_id'          => $this->project->id,
                'dataset_row_id'      => $rowId,
                'user_id'             => auth()->id(),
                'annotation_field_id' => $fieldId,
            ],
            ['value' => $value]
        );

        // Update local state
        $this->annotations[$rowId][$fieldId] = $value;

        // Check if row is now complete
        $this->chunkService->markRowCompleted($row, $this->project);

        // Flash saved indicator
        $this->savedRows[$rowId] = true;
    }

    public function getAnnotationValue(int $rowId, int $fieldId): mixed
    {
        return $this->annotations[$rowId][$fieldId] ?? null;
    }

    public function setActiveRow(int $rowId): void
    {
        $this->activeRowId = $rowId;
    }

    public function render(): View
    {
        $fields = $this->project->annotationFields()->orderBy('order')->get();

        $rows = DatasetRow::with(['rowAssignments' => function ($q) {
                $q->where('project_id', $this->project->id)
                  ->where('user_id', auth()->id());
            }])
            ->where('dataset_id', $this->project->dataset_id)
            ->whereHas('rowAssignments', function ($query) {
                $query->where('project_id', $this->project->id)
                      ->where('user_id', auth()->id());
            })
            ->orderBy('row_index')
            ->paginate(25);

        $this->project->loadMissing('dataset');

        return view('livewire.projects.annotation-workspace', compact('fields', 'rows'));
    }
}
