<x-app-layout>
    <x-slot name="pageTitle">My Projects</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">My Projects</h2>
                <p class="text-sm text-gray-500 mt-1">Projects you own and manage.</p>
            </div>
            <a href="{{ route('projects.create') }}" wire:navigate class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Project
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success mb-6">{{ session('success') }}</div>
        @endif

        @if($ownedProjects->isEmpty())
            <div class="card">
                <div class="empty-state py-16">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h4 class="text-gray-700 font-medium text-lg">No projects yet</h4>
                    <p class="text-gray-500 text-sm mt-2">Create your first annotation project to get started.</p>
                    <a href="{{ route('projects.create') }}" wire:navigate class="btn-primary mt-4">
                        Create First Project
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($ownedProjects as $project)
                    @php
                        $total     = $project->dataset->row_count;
                        $completed = $project->dataset->rows()->where('status', 'completed')->count();
                        $percent   = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <div class="card hover:shadow-md transition-all">
                        <div class="card-body">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $project->name }}</h3>
                                    @if($project->description)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $project->description }}</p>
                                    @endif
                                </div>
                                <div class="ml-3 flex-shrink-0">
                                    @if($project->status === 'active')
                                        <span class="badge-green">Active</span>
                                    @elseif($project->status === 'completed')
                                        <span class="badge-indigo">Completed</span>
                                    @else
                                        <span class="badge-gray">Archived</span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-gray-500 mb-4">
                                <div class="flex justify-between">
                                    <span>Dataset</span>
                                    <span class="font-medium text-gray-700">{{ $project->dataset->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Annotators</span>
                                    <span class="font-medium text-gray-700">{{ $project->annotators_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Progress</span>
                                    <span class="font-medium text-gray-700">{{ $percent }}%</span>
                                </div>
                            </div>

                            <div class="progress-bar h-1.5 mb-4">
                                <div class="progress-fill h-1.5" style="width: {{ $percent }}%"></div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('projects.show', $project) }}" wire:navigate
                                   class="btn-primary btn-sm flex-1 justify-center">Open</a>
                                <a href="{{ route('projects.edit', $project) }}" wire:navigate
                                   class="btn-secondary btn-sm">Edit</a>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="btn-ghost btn-sm px-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 5v.01M12 12v.01M12 19v.01"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10">
                                        <a href="#"
                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Export CSV
                                        </a>
                                        <a href="#"
                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Export XLSX
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                              onsubmit="return confirm('Delete this project?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
