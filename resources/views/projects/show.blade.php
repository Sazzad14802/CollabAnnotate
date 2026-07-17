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
        <div x-data="{ tab: '{{ request('tab', 'workspace') }}' }">
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <button @click="tab = 'workspace'"
                            :class="tab === 'workspace' ? 'nav-link active' : 'nav-link'"
                            type="button">Annotation Workspace</button>
                </li>
                <li class="nav-item">
                    <button @click="tab = 'progress'"
                            :class="tab === 'progress' ? 'nav-link active' : 'nav-link'"
                            type="button">Progress &amp; Stats</button>
                </li>
                @can('manageSchema', $project)
                    <li class="nav-item">
                        <button @click="tab = 'annotated_rows'"
                                :class="tab === 'annotated_rows' ? 'nav-link active' : 'nav-link'"
                                type="button">Dataset Rows</button>
                    </li>
                    <li class="nav-item">
                        <button @click="tab = 'annotators'"
                                :class="tab === 'annotators' ? 'nav-link active' : 'nav-link'"
                                type="button">Annotators</button>
                    </li>
                @endcan

            </ul>

            {{-- Workspace Tab --}}
            <div x-show="tab === 'workspace'" x-cloak>
                @if($project->annotationFields()->count() === 0)
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h6 class="fw-semibold">No annotation schema defined yet</h6>
                            <p class="text-muted small mt-1">
                                No annotation schema was defined for this project.
                            </p>
                        </div>
                    </div>
                @elseif($project->import_status !== 'completed')
                    <div class="alert alert-info">
                        Dataset is still importing (status: {{ $project->import_status }}). Please wait.
                    </div>
                @else
                    <livewire:projects.annotation-workspace :project="$project" />
                @endif
            </div>

            {{-- Progress Tab --}}
            <div x-show="tab === 'progress'" x-cloak>
                <livewire:projects.progress-tracker :project="$project" />
            </div>

            {{-- Annotators & Dataset Rows Tabs --}}
            @can('manageSchema', $project)
                <div x-show="tab === 'annotated_rows'" x-cloak>
                    <livewire:projects.annotated-rows :project="$project" />
                </div>
                
                <div x-show="tab === 'annotators'" x-cloak>
                    <livewire:projects.annotator-manager :project="$project" />
                </div>
            @endcan


        </div>
    </div>
</x-app-layout>
