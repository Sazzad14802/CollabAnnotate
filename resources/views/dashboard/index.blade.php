<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        {{-- Welcome Banner --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name ?? 'User' }} 👋</h2>
            <p class="text-gray-500 mt-1">Here's an overview of your annotation workspace.</p>
        </div>

        <div class="card p-12 text-center text-gray-500">
            <p>Your dashboard is currently empty. Datasets and Projects will appear here soon!</p>
        </div>
    </div>
</x-app-layout>
