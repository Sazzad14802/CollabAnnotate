<x-app-layout>
    <x-slot name="pageTitle">{{ $dataset->name }}</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('datasets.index') }}" wire:navigate
               class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">{{ $dataset->name }}</h2>
            @if($dataset->import_status === 'completed')
                <span class="badge-green">Imported</span>
            @elseif($dataset->import_status === 'processing')
                <span class="badge-yellow">Processing...</span>
            @else
                <span class="badge-red">{{ ucfirst($dataset->import_status) }}</span>
            @endif
        </div>

        {{-- Dataset Info Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-gray-900">{{ number_format($dataset->row_count) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Rows</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ count($dataset->column_names) }}</p>
                <p class="text-sm text-gray-500 mt-1">Columns</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $dataset->projects->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Projects</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-sm font-medium text-gray-900">{{ $dataset->created_at->format('M d, Y') }}</p>
                <p class="text-sm text-gray-500 mt-1">Uploaded</p>
            </div>
        </div>

        {{-- Columns --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Columns</h3>
            </div>
            <div class="card-body">
                <div class="flex flex-wrap gap-2">
                    @foreach($dataset->column_names as $col)
                        <span class="badge-gray">{{ $col }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Data Preview --}}
        <div class="card overflow-x-auto">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Data Preview</h3>
                <p class="text-sm text-gray-500 mt-1">Showing {{ $rows->count() }} of {{ $dataset->row_count }} rows</p>
            </div>
            <table class="data-table min-w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach($dataset->column_names as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr wire:key="drow-{{ $row->id }}">
                            <td class="text-gray-400 text-xs font-mono">{{ $row->row_index + 1 }}</td>
                            @foreach($dataset->column_names as $col)
                                <td>
                                    <div class="max-w-xs truncate" title="{{ $row->data[$col] ?? '' }}">
                                        {{ $row->data[$col] ?? '—' }}
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
