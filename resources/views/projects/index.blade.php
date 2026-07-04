<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">My Projects</h2>
    </x-slot>

    <div class="container-fluid py-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">My Projects</h4>
                <p class="text-muted small mb-0">Projects you own and manage.</p>
            </div>
            <a href="{{ route('projects.create') }}" wire:navigate class="btn btn-primary">
                + New Project
            </a>
        </div>



        @if($ownedProjects->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h6 class="fw-semibold mb-0">No projects yet</h6>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($ownedProjects as $project)
                    @php
                        $total     = $project->dataset->row_count;
                        $completed = $project->rowAssignments()->where('status', 'completed')->count();
                        $percent   = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-1 me-2" style="min-width:0;">
                                        <h6 class="fw-semibold mb-0 text-truncate">{{ $project->name }}</h6>
                                        @if($project->description)
                                            <small class="text-muted">{{ Str::limit($project->description, 60) }}</small>
                                        @endif
                                    </div>
                                    @if($project->status === 'active')
                                        <span class="badge badge-green rounded-pill">Active</span>
                                    @elseif($project->status === 'completed')
                                        <span class="badge badge-indigo rounded-pill">Completed</span>
                                    @else
                                        <span class="badge badge-gray rounded-pill">Archived</span>
                                    @endif
                                </div>

                                <div class="small text-muted mb-3">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Dataset</span>
                                        <span class="text-dark fw-medium">{{ $project->dataset->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Annotators</span>
                                        <span class="text-dark fw-medium">{{ $project->annotators_count }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Progress</span>
                                        <span class="text-dark fw-medium">{{ $percent }}%</span>
                                    </div>
                                </div>

                                <div class="progress mb-3" style="height:5px;">
                                    <div class="progress-bar progress-bar-indigo" style="width:{{ $percent }}%"></div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('projects.show', $project) }}" wire:navigate
                                       class="btn btn-primary btn-sm flex-fill text-center">Open</a>
                                    <a href="{{ route('projects.edit', $project) }}" wire:navigate
                                       class="btn btn-outline-secondary btn-sm">Edit</a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                            ···
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#">Export CSV</a></li>
                                            <li><a class="dropdown-item" href="#">Export XLSX</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                                      onsubmit="return confirm('Delete this project?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
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
