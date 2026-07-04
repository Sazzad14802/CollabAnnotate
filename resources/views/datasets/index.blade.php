<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Upload and manage your datasets</h2>

    </x-slot>

    <div class="container-fluid py-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('datasets.create') }}" wire:navigate class="btn btn-primary">
                + Upload Dataset
            </a>
        </div>



        <livewire:dashboard.my-datasets />
    </div>
</x-app-layout>
