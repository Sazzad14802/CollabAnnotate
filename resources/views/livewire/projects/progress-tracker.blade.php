<div wire:poll.5s>
    {{-- Overall Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <h3 class="fw-bold mb-0">{{ number_format($total) }}</h3>
                    <small class="text-muted">Total Rows</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($completed) }}</h3>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($remaining) }}</h3>
                    <small class="text-muted">Remaining</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center h-100">
                <div class="card-body py-4">
                    <h3 class="fw-bold mb-0 text-primary">{{ $percent }}%</h3>
                    <small class="text-muted">Progress</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between small fw-medium mb-2">
                <span>Overall Progress</span>
                <span>{{ $completed }} / {{ $total }}</span>
            </div>
            <div class="progress" style="height:16px;">
                <div class="progress-bar progress-bar-indigo fw-medium" style="width:{{ $percent }}%">
                    {{ $percent > 10 ? $percent . '%' : '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Annotator Stats Table --}}
    <div class="card">
        <div class="card-header fw-semibold">Annotator Statistics</div>
        @if($annotatorStats->isEmpty())
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">No annotators assigned to this project.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Annotator</th>
                            <th>Assigned Rows</th>
                            <th>Annotated Rows</th>
                            <th>Progress</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($annotatorStats as $stat)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-semibold"
                                             style="width:28px;height:28px;font-size:12px;">
                                            {{ substr($stat['name'], 0, 1) }}
                                        </div>
                                        <span class="fw-medium">{{ $stat['name'] }}</span>
                                    </div>
                                </td>
                                <td>{{ number_format($stat['assigned']) }}</td>
                                <td>{{ number_format($stat['annotated']) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height:6px;">
                                            <div class="progress-bar progress-bar-indigo" style="width:{{ $stat['percent'] }}%"></div>
                                        </div>
                                        <small class="text-muted" style="width:35px;">{{ $stat['percent'] }}%</small>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $stat['last_activity'] ? \Carbon\Carbon::parse($stat['last_activity'])->diffForHumans() : 'No activity' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
