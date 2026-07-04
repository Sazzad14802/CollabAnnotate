<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">{{ $dataset->name }}</h2>
    </x-slot>

    <div class="container-fluid py-4 px-4">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('datasets.index') }}" wire:navigate class="text-muted">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h4 class="fw-bold mb-0">{{ $dataset->name }}</h4>
            @if($dataset->import_status === 'completed')
                <span class="badge badge-green rounded-pill">Imported</span>
            @elseif($dataset->import_status === 'processing')
                <span class="badge badge-yellow rounded-pill">Processing...</span>
            @else
                <span class="badge badge-red rounded-pill">{{ ucfirst($dataset->import_status) }}</span>
            @endif
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-sm-3">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <h3 class="fw-bold mb-0">{{ number_format($dataset->row_count) }}</h3>
                        <small class="text-muted">Total Rows</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <h3 class="fw-bold mb-0 text-primary">{{ count($dataset->column_names) }}</h3>
                        <small class="text-muted">Columns</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <h3 class="fw-bold mb-0">{{ $dataset->projects->count() }}</h3>
                        <small class="text-muted">Projects</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <p class="fw-semibold mb-0">{{ $dataset->created_at->format('M d, Y') }}</p>
                        <small class="text-muted">Uploaded</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columns --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Columns</div>
            <div class="card-body d-flex flex-wrap gap-2">
                @foreach($dataset->column_names as $col)
                    <span class="badge badge-gray">{{ $col }}</span>
                @endforeach
            </div>
        </div>

        {{-- Data Preview --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Data Preview</span>
                <small class="text-muted">Showing {{ $rows->count() }} of {{ $dataset->row_count }} rows</small>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
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
                                <td class="text-muted small font-monospace">{{ $row->row_index + 1 }}</td>
                                @foreach($dataset->column_names as $col)
                                    <td>
                                        {{ $row->data[$col] ?? '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
