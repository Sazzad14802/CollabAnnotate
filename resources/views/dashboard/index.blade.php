<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        {{-- Welcome Banner --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name ?? 'User' }} 👋</h2>
            <p class="text-gray-500 mt-1">Here's an overview of your annotation workspace.</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-success mb-6">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- My Datasets --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Datasets</h3>
                <div class="flex items-center gap-3">
                    <a href="{{ route('datasets.create') }}" wire:navigate class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Dataset
                    </a>
                </div>
            </div>
            <livewire:dashboard.my-datasets />
        </div>

        <div class="card p-12 text-center text-gray-500">
            <p>Your dashboard is currently empty. Datasets and Projects will appear here soon!</p>
        </div>
    </div>
</x-app-layout>
