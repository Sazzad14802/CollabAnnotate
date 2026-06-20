<x-app-layout>
    <x-slot name="pageTitle">Edit Project</x-slot>

    <div class="page-section max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('projects.show', $project) }}" wire:navigate
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Project
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Project Settings</h2>
            </div>
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body space-y-5">
                    <div>
                        <label for="edit-name" class="form-label">Project Name</label>
                        <input type="text" id="edit-name" name="name"
                               value="{{ old('name', $project->name) }}" class="form-input" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-desc" class="form-label">Description</label>
                        <textarea id="edit-desc" name="description" rows="3"
                                  class="form-input">{{ old('description', $project->description) }}</textarea>
                    </div>
                    <div>
                        <label for="edit-chunk" class="form-label">Chunk Size</label>
                        <input type="number" id="edit-chunk" name="chunk_size"
                               value="{{ old('chunk_size', $project->chunk_size) }}"
                               min="1" max="1000" class="form-input w-32">
                    </div>
                    <div>
                        <label for="edit-status" class="form-label">Status</label>
                        <select id="edit-status" name="status" class="form-select w-40">
                            <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('projects.show', $project) }}" wire:navigate class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="card mt-6 border-red-200">
            <div class="card-header border-red-100">
                <h3 class="font-semibold text-red-700">Danger Zone</h3>
            </div>
            <div class="card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Delete this project</p>
                    <p class="text-xs text-gray-500 mt-0.5">This will permanently delete the project and all annotations.</p>
                </div>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Delete project permanently? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">Delete Project</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
