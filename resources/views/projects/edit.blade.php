<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Edit Project</h2>
    </x-slot>

    <div class="container py-4" style="max-width:600px;">
        <div class="mb-3">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="text-muted small d-inline-flex align-items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Project
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="fw-semibold mb-0">Project Settings</h5>
            </div>
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body">

                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Project Name</label>
                        <input type="text" id="edit-name" name="name"
                               value="{{ old('name', $project->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit-desc" class="form-label">Description</label>
                        <textarea id="edit-desc" name="description" rows="3"
                                  class="form-control">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit-chunk" class="form-label">Chunk Size</label>
                        <input type="number" id="edit-chunk" name="chunk_size"
                               value="{{ old('chunk_size', $project->chunk_size) }}"
                               min="1" max="1000" class="form-control" style="width:120px;">
                    </div>

                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select id="edit-status" name="status" class="form-select" style="width:180px;">
                            <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('projects.show', $project) }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="card border-danger">
            <div class="card-header text-danger fw-semibold">Danger Zone</div>
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="fw-medium mb-0">Delete this project</p>
                    <small class="text-muted">This will permanently delete the project and all annotations.</small>
                </div>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Delete project permanently? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete Project</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
