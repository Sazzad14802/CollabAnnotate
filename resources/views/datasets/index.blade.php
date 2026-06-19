<x-app-layout>
    <x-slot name="pageTitle">Datasets</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Datasets</h2>
                <p class="text-sm text-gray-500 mt-1">Upload and manage your datasets.</p>
            </div>
            <a href="{{ route('datasets.create') }}" wire:navigate class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Upload Dataset
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success mb-6">{{ session('success') }}</div>
        @endif

        <livewire:dashboard.my-datasets />
    </div>
</x-app-layout>
