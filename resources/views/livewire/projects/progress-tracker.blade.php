<div class="space-y-6">
    {{-- Overall Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card text-center">
            <p class="text-3xl font-bold text-gray-900">{{ number_format($total) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Rows</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-3xl font-bold text-green-600">{{ number_format($completed) }}</p>
            <p class="text-sm text-gray-500 mt-1">Completed</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-3xl font-bold text-amber-500">{{ number_format($remaining) }}</p>
            <p class="text-sm text-gray-500 mt-1">Remaining</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $percent }}%</p>
            <p class="text-sm text-gray-500 mt-1">Progress</p>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="card card-body">
        <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
            <span>Overall Progress</span>
            <span>{{ $completed }} / {{ $total }}</span>
        </div>
        <div class="progress-bar h-4 rounded-full">
            <div class="progress-fill h-4 rounded-full" style="width: {{ $percent }}%"></div>
        </div>
    </div>

    {{-- Annotator Stats Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Annotator Statistics</h3>
        </div>
        @if($annotatorStats->isEmpty())
            <div class="empty-state py-10">
                <p class="text-gray-500 text-sm">No annotators assigned to this project.</p>
            </div>
        @else
            <table class="data-table">
                <thead>
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
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-xs">
                                        {{ substr($stat['name'], 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $stat['name'] }}</span>
                                </div>
                            </td>
                            <td>{{ number_format($stat['assigned']) }}</td>
                            <td>{{ number_format($stat['annotated']) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="progress-bar h-2 w-24">
                                        <div class="progress-fill h-2" style="width: {{ $stat['percent'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 w-10">{{ $stat['percent'] }}%</span>
                                </div>
                            </td>
                            <td class="text-gray-500 text-sm">
                                {{ $stat['last_activity'] ? \Carbon\Carbon::parse($stat['last_activity'])->diffForHumans() : 'No activity' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
