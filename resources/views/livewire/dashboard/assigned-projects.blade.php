<div>
    @if($projects->isEmpty())
        <div class="card">
            <div class="empty-state py-10">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p class="text-gray-500 text-sm">You are not assigned to any projects yet.</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($projects as $project)
                @php
                    $total     = $project->dataset->row_count;
                    $myRows    = $project->dataset->rows()->where('assigned_to', auth()->id())->count();
                    $myDone    = $project->dataset->rows()->where('assigned_to', auth()->id())->where('status', 'completed')->count();
                    $pending   = $myRows - $myDone;
                    $percent   = $myRows > 0 ? round(($myDone / $myRows) * 100) : 0;
                @endphp
                <div class="card hover:shadow-md transition-shadow">
                    <div class="card-body">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $project->name }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">by {{ $project->owner->name }}</p>
                            </div>
                            <span class="badge-indigo">Annotator</span>
                        </div>

                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>My Progress</span>
                                <span>{{ $myDone }} / {{ $myRows }} rows</span>
                            </div>
                            <div class="progress-bar h-2">
                                <div class="progress-fill h-2" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span class="{{ $pending > 0 ? 'text-amber-600 font-medium' : 'text-green-600 font-medium' }}">
                                {{ $pending > 0 ? "{$pending} rows pending" : '✓ All done!' }}
                            </span>
                            <span>{{ $project->pivot->joined_at ? \Carbon\Carbon::parse($project->pivot->joined_at)->format('M d, Y') : '' }}</span>
                        </div>

                        <a href="{{ route('projects.show', $project) }}" wire:navigate
                           class="btn-primary btn-sm w-full justify-center">
                            Open Workspace
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
