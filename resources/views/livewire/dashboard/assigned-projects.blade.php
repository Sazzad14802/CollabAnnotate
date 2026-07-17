<div>
    @if($projects->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <h6 class="fw-semibold">No assigned projects</h6>
                <p class="text-muted small">You haven't been assigned to any annotation projects yet.</p>
            </div>
        </div>
    @else
        <div class="mb-4">
            <h5 class="fw-semibold mb-3">Assigned to Me</h5>
            <div class="row g-3">
                @foreach($projects as $project)
                    @php
                        $total     = $project->row_count;
                        $myRows    = $project->rowAssignments()->where('user_id', auth()->id())->count();
                        $myDone    = $project->rowAssignments()->where('user_id', auth()->id())->where('status', 'completed')->count();
                        $pending   = $myRows - $myDone;
                        $percent   = $myRows > 0 ? round(($myDone / $myRows) * 100) : 0;
                        
                        $overallCompleted = $project->rowAssignments()->where('status', 'completed')->count();
                        $overallPercent   = $total > 0 ? round(($overallCompleted / $total) * 100) : 0;
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ $project->name }}</h6>
                                        <small class="text-muted">by {{ $project->owner->email }}</small>
                                    </div>
                                    <span class="badge badge-indigo rounded-pill">Annotator</span>
                                </div>

                                <div class="mb-2">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>My Progress</span>
                                        <span>{{ $myDone }} / {{ $myRows }} rows</span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar progress-bar-indigo" style="width:{{ $percent }}%"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Overall Progress</span>
                                        <span>{{ $overallCompleted }} / {{ $total }} rows</span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-success" style="width:{{ $overallPercent }}%"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between small mb-3">
                                    <span class="{{ $pending > 0 ? 'text-warning fw-medium' : 'text-success fw-medium' }}">
                                        {{ $pending > 0 ? "{$pending} rows pending" : '✓ All done!' }}
                                    </span>
                                </div>

                                <a href="{{ route('projects.show', $project) }}" wire:navigate
                                   class="btn btn-primary btn-sm w-100">Open Workspace</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
