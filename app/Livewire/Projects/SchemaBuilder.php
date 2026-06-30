<?php

namespace App\Livewire\Projects;

use App\Models\AnnotationField;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class SchemaBuilder extends Component
{
    public Project $project;

    // Form state for adding a new field
    public string $fieldName    = '';
    public string $fieldType    = 'select';
    public string $optionsInput = ''; // comma-separated options
    public bool   $isRequired   = false;

    public bool $editing = false;
    public ?int $editingId = null;

    protected function rules(): array
    {
        return [
            'fieldName'    => ['required', 'string', 'max:100'],
            'fieldType'    => ['required', 'in:select,checkbox'],
            'optionsInput' => ['required_if:fieldType,select', 'nullable', 'string'],
            'isRequired'   => ['boolean'],
        ];
    }

    public function saveField(): void
    {
        $this->authorize('manageSchema', $this->project);
        $this->validate();

        $slug    = str($this->fieldName)->slug()->toString();
        $options = null;

        if ($this->fieldType === 'select') {
            $options = array_filter(array_map('trim', explode(',', $this->optionsInput)));
            if (empty($options)) {
                $this->addError('optionsInput', 'Please provide at least one option.');
                return;
            }
            $options = array_values($options);
        }

        $maxOrder = $this->project->annotationFields()->max('order') ?? -1;

        if ($this->editing && $this->editingId) {
            AnnotationField::where('id', $this->editingId)
                ->where('project_id', $this->project->id)
                ->update([
                    'name'        => $this->fieldName,
                    'slug'        => $slug,
                    'type'        => $this->fieldType,
                    'options'     => $options,
                    'is_required' => $this->isRequired,
                ]);
        } else {
            AnnotationField::create([
                'project_id'  => $this->project->id,
                'name'        => $this->fieldName,
                'slug'        => $slug,
                'type'        => $this->fieldType,
                'options'     => $options,
                'is_required' => $this->isRequired,
                'order'       => $maxOrder + 1,
            ]);
        }

        $this->resetForm();
        $this->dispatch('field-saved');
    }

    public function editField(int $id): void
    {
        $field = AnnotationField::findOrFail($id);
        $this->editing    = true;
        $this->editingId  = $id;
        $this->fieldName  = $field->name;
        $this->fieldType  = $field->type;
        $this->optionsInput = $field->options ? implode(', ', $field->options) : '';
        $this->isRequired = $field->is_required;
    }

    public function deleteField(int $id): void
    {
        $this->authorize('manageSchema', $this->project);
        AnnotationField::where('id', $id)->where('project_id', $this->project->id)->delete();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->fieldName    = '';
        $this->fieldType    = 'select';
        $this->optionsInput = '';
        $this->isRequired   = false;
        $this->editing      = false;
        $this->editingId    = null;
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $fields = $this->project->annotationFields()->orderBy('order')->get();
        return view('livewire.projects.schema-builder', compact('fields'));
    }
}
