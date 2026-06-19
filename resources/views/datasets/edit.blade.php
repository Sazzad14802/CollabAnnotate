<x-app-layout>
    <x-slot name="pageTitle">Edit Dataset</x-slot>

    <div class="page-section max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('datasets.show', $dataset) }}" wire:navigate
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dataset
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Edit Dataset</h2>
            </div>
            <form action="{{ route('datasets.update', $dataset) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body">
                    <label for="edit-ds-name" class="form-label">Dataset Name</label>
                    <input type="text" id="edit-ds-name" name="name"
                           value="{{ old('name', $dataset->name) }}" class="form-input" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="modal-footer">
                    <a href="{{ route('datasets.show', $dataset) }}" wire:navigate class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
