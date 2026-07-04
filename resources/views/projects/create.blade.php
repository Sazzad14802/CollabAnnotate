<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Create Project</h2>
    </x-slot>

    <div class="container py-4" style="max-width:700px;">
        <div class="mb-3">
            <a href="{{ route('projects.index') }}" wire:navigate class="text-muted small d-inline-flex align-items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Projects
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="fw-semibold mb-0">Create Annotation Project</h5>
                <p class="text-muted small mb-0">Set up a new project with a dataset to start annotating.</p>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Project Name --}}
                    <div class="mb-3">
                        <label for="proj-name" class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" id="proj-name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Sentiment Analysis 2024" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="proj-desc" class="form-label">Description</label>
                        <textarea id="proj-desc" name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the goal of this annotation project...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Dataset Source --}}
                    <div class="mb-3">
                        <label for="dataset-id" class="form-label">Dataset <span class="text-danger">*</span></label>
                        <select id="dataset-id" name="dataset_id" class="form-select" required>
                            <option value="">-- Choose a dataset --</option>
                            @foreach($datasets as $dataset)
                                <option value="{{ $dataset->id }}" {{ old('dataset_id') == $dataset->id ? 'selected' : '' }}>
                                    {{ $dataset->name }} ({{ number_format($dataset->row_count) }} rows)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">You must select an existing, fully imported dataset.</div>
                        @error('dataset_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Annotation Schema --}}
                    <div class="mb-4 pt-3 border-top" x-data="{
                        fields: [
                            { name: 'Sentiment', type: 'select', options: 'Positive, Negative, Neutral', is_required: true }
                        ],
                        addField() {
                            this.fields.push({ name: '', type: 'select', options: '', is_required: false });
                        },
                        removeField(index) {
                            this.fields.splice(index, 1);
                        }
                    }">
                        <h6 class="fw-semibold mb-1">Annotation Schema <span class="text-danger">*</span></h6>
                        <p class="text-muted small mb-3">Define the fields annotators will fill out. <strong class="text-danger">This cannot be changed later.</strong></p>

                        <div class="d-flex flex-column gap-3 mb-3">
                            <template x-for="(field, index) in fields" :key="index">
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-medium small" x-text="'Field ' + (index + 1)"></span>
                                            <button type="button" class="btn btn-sm text-danger p-0" @click="removeField(index)" x-show="fields.length > 1">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" x-model="field.name" :name="'schema['+index+'][name]'" class="form-control form-control-sm" placeholder="Field Name (e.g. Sentiment)" required>
                                            </div>
                                            <div class="col-md-3">
                                                <select x-model="field.type" :name="'schema['+index+'][type]'" class="form-select form-select-sm" required>
                                                    <option value="select">Dropdown (Select)</option>
                                                    <option value="checkbox">Checkbox (Yes/No)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" x-model="field.options" :name="'schema['+index+'][options]'" x-show="field.type === 'select'" class="form-control form-control-sm" placeholder="Comma separated options" :required="field.type === 'select'">
                                            </div>
                                        </div>
                                        <div class="mt-2 form-check">
                                            <input type="hidden" :name="'schema['+index+'][is_required]'" value="0">
                                            <input class="form-check-input" type="checkbox" value="1" x-model="field.is_required" :name="'schema['+index+'][is_required]'" :id="'req-'+index">
                                            <label class="form-check-label small" :for="'req-'+index">Required field</label>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary" @click="addField">
                            + Add Field
                        </button>
                        
                        @error('schema') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @error('schema.*') <div class="text-danger small mt-1">Invalid schema definition.</div> @enderror
                    </div>

                    {{-- Chunk Size --}}
                    <div class="mb-3">
                        <label for="chunk-size" class="form-label">Chunk Size</label>
                        <input type="number" id="chunk-size" name="chunk_size"
                               value="{{ old('chunk_size', 10) }}" min="1" max="1000"
                               class="form-control" style="width:120px;">
                        <div class="form-text">Number of rows assigned to each annotator per batch.</div>
                        @error('chunk_size') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('projects.index') }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
