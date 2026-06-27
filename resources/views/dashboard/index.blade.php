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

        {{-- Assigned to Me (Annotator View) --}}
        <livewire:dashboard.assigned-projects />

        {{-- Projects Section --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Projects</h3>
                <a href="{{ route('projects.create') }}" wire:navigate class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Project
                </a>
            </div>
            @php
                $ownedProjects = auth()->user()->ownedProjects()
                    ->with(['dataset'])
                    ->withCount('annotators')
                    ->latest()
                    ->limit(6)
                    ->get();
            @endphp

            @if($ownedProjects->isEmpty())
                <div class="card">
                    <div class="empty-state py-12">
                        <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h4 class="text-gray-700 font-medium">No projects yet</h4>
                        <p class="text-gray-500 text-sm mt-1">Create your first annotation project to get started.</p>
                        <a href="{{ route('projects.create') }}" wire:navigate class="btn-primary mt-4">Create Project</a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ownedProjects as $project)
                        @php
                            $total     = $project->dataset->row_count;
                            $completed = $project->dataset->rows()->where('status', 'completed')->count();
                            $percent   = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        <div class="card hover:shadow-md transition-shadow">
                            <div class="card-body">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $project->name }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $project->dataset->name }}</p>
                                    </div>
                                    <span class="badge-{{ $project->status === 'active' ? 'green' : 'gray' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span>{{ $completed }} / {{ $total }}</span>
                                    </div>
                                    <div class="progress-bar h-2">
                                        <div class="progress-fill h-2" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $percent }}% complete</p>
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                    <span>0 annotator(s)</span>
                                    <span>{{ $project->created_at->format('M d, Y') }}</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('projects.show', $project) }}" wire:navigate
                                       class="btn-primary btn-sm flex-1 justify-center">Open</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
</x-app-layout>
