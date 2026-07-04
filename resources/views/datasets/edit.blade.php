<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Rename Dataset</h2>
    </x-slot>

    <div class="container py-4" style="max-width:500px;">
        <div class="mb-3">
            <a href="{{ route('datasets.index') }}" wire:navigate class="text-muted small d-inline-flex align-items-center gap-1">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Datasets
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="fw-semibold mb-0">Rename Dataset</h5>
            </div>
            <form action="{{ route('datasets.update', $dataset) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="edit-ds-name" class="form-label">Dataset Name</label>
                        <input type="text" id="edit-ds-name" name="name"
                               value="{{ old('name', $dataset->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('datasets.index') }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
