<x-app-layout>
    <x-slot name="pageTitle">Create Project</x-slot>

    <div class="page-section max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('projects.index') }}" wire:navigate
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Projects
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Create Annotation Project</h2>
                <p class="text-sm text-gray-500 mt-1">Set up a new project with a dataset to start annotating.</p>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body space-y-6">

                    {{-- Project Name --}}
                    <div>
                        <label for="proj-name" class="form-label">Project Name <span class="text-red-500">*</span></label>
                        <input type="text" id="proj-name" name="name" value="{{ old('name') }}"
                               class="form-input" placeholder="e.g. Sentiment Analysis 2024" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="proj-desc" class="form-label">Description</label>
                        <textarea id="proj-desc" name="description" rows="3"
                                  class="form-input" placeholder="Describe the goal of this annotation project...">{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dataset Source --}}
                    <div x-data="{ source: '{{ old('dataset_source', 'upload') }}' }">
                        <label class="form-label">Dataset <span class="text-red-500">*</span></label>

                        <div class="flex gap-4 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="dataset_source" value="upload"
                                       x-model="source" class="text-indigo-600">
                                <span class="text-sm font-medium text-gray-700">Upload new file</span>
                            </label>
                            @if($datasets->isNotEmpty())
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="dataset_source" value="existing"
                                           x-model="source" class="text-indigo-600">
                                    <span class="text-sm font-medium text-gray-700">Use existing dataset</span>
                                </label>
                            @endif
                        </div>

                        {{-- Upload new --}}
                        <div x-show="source === 'upload'" class="space-y-4">
                            <div>
                                <label for="dataset-name" class="form-label">Dataset Name</label>
                                <input type="text" id="dataset-name" name="dataset_name"
                                       value="{{ old('dataset_name') }}" class="form-input"
                                       placeholder="My Training Dataset">
                                @error('dataset_name') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="dataset-file" class="form-label">CSV or XLSX File</label>
                                <div class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-200 border-dashed rounded-xl hover:border-indigo-400 transition-colors">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <div class="mt-3">
                                            <label for="dataset-file" class="cursor-pointer">
                                                <span class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">Choose file</span>
                                                <span class="text-gray-500 text-sm"> or drag and drop</span>
                                            </label>
                                            <input id="dataset-file" name="dataset_file" type="file"
                                                   accept=".csv,.xlsx" class="sr-only">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">CSV or XLSX up to 50MB</p>
                                    </div>
                                </div>
                                @error('dataset_file') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Existing dataset --}}
                        <div x-show="source === 'existing'" x-cloak>
                            <label for="dataset-id" class="form-label">Select Dataset</label>
                            <select id="dataset-id" name="dataset_id" class="form-select">
                                <option value="">-- Choose a dataset --</option>
                                @foreach($datasets as $dataset)
                                    <option value="{{ $dataset->id }}" {{ old('dataset_id') == $dataset->id ? 'selected' : '' }}>
                                        {{ $dataset->name }} ({{ number_format($dataset->row_count) }} rows)
                                    </option>
                                @endforeach
                            </select>
                            @error('dataset_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Chunk Size --}}
                    <div>
                        <label for="chunk-size" class="form-label">Chunk Size</label>
                        <input type="number" id="chunk-size" name="chunk_size"
                               value="{{ old('chunk_size', 50) }}" min="1" max="1000"
                               class="form-input w-32">
                        <p class="text-xs text-gray-500 mt-1">Number of rows assigned to each annotator per batch.</p>
                        @error('chunk_size') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('projects.index') }}" wire:navigate class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
