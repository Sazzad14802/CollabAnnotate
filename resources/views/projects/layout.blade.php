<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">{{ $project->name }}</h2>
    </x-slot>

    <div class="container-fluid py-4 px-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if($project->user_id === auth()->id())
                        <a href="{{ route('projects.index') }}" wire:navigate class="text-muted text-decoration-none">
                            &larr; Back
                        </a>
                    @else
                        <a href="{{ route('projects.assigned') }}" wire:navigate class="text-muted text-decoration-none">
                            &larr; Back
                        </a>
                    @endif
                    <h4 class="fw-bold mb-0">{{ $project->name }}</h4>
                </div>
                @if($project->description)
                    <p class="text-muted small ms-4 mb-0">{{ $project->description }}</p>
                @endif
            </div>

            {{-- Actions --}}
            @can('update', $project)
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('projects.export', ['project' => $project->id, 'format' => 'csv']) }}">CSV</a></li>
                            <li><a class="dropdown-item" href="{{ route('projects.export', ['project' => $project->id, 'format' => 'xlsx']) }}">XLSX</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('projects.edit', $project) }}" wire:navigate class="btn btn-outline-secondary btn-sm">Settings</a>
                </div>
            @endcan
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a href="{{ route('projects.show', $project) }}" wire:navigate
                   class="nav-link {{ request()->routeIs('projects.show') ? 'active' : '' }}">
                    Annotation Workspace
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('projects.progress', $project) }}" wire:navigate
                   class="nav-link {{ request()->routeIs('projects.progress') ? 'active' : '' }}">
                    Progress &amp; Stats
                </a>
            </li>
            @can('manageSchema', $project)
                <li class="nav-item">
                    <a href="{{ route('projects.rows', $project) }}" wire:navigate
                       class="nav-link {{ request()->routeIs('projects.rows') ? 'active' : '' }}">
                        Dataset Rows
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.annotators', $project) }}" wire:navigate
                       class="nav-link {{ request()->routeIs('projects.annotators') ? 'active' : '' }}">
                        Annotators
                    </a>
                </li>
            @endcan
        </ul>

        {{-- Content --}}
        <div>
            @yield('project_content')
        </div>
    </div>
</x-app-layout>
