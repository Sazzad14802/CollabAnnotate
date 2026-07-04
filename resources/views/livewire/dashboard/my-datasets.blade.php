<div>
    {{-- Search --}}
    <div class="mb-3">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Search datasets..."
               class="form-control"
               style="max-width:300px;"
               id="dataset-search-input">
    </div>

    @if($datasets->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <p class="text-muted mb-0">No datasets found. Upload your first dataset.</p>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Dataset Name</th>
                        <th>Rows</th>
                        <th>Columns</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datasets as $dataset)
                        <tr wire:key="dataset-{{ $dataset->id }}">
                            <td>
                                <div class="fw-medium">{{ $dataset->name }}</div>
                                <small class="text-muted">{{ $dataset->original_filename }}</small>
                            </td>
                            <td>{{ number_format($dataset->row_count) }}</td>
                            <td>{{ count($dataset->column_names) }}</td>
                            <td>
                                @if($dataset->import_status === 'completed')
                                    <span class="badge badge-green rounded-pill">Imported</span>
                                @elseif($dataset->import_status === 'processing')
                                    <span class="badge badge-yellow rounded-pill">Processing...</span>
                                @elseif($dataset->import_status === 'pending')
                                    <span class="badge badge-gray rounded-pill">Pending</span>
                                @else
                                    <span class="badge badge-red rounded-pill">Failed</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $dataset->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('datasets.show', $dataset) }}" wire:navigate
                                       class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('datasets.edit', $dataset) }}" wire:navigate
                                       class="btn btn-sm btn-outline-secondary">Rename</a>
                                    <button wire:click="deleteDataset({{ $dataset->id }})"
                                            wire:confirm="Delete dataset '{{ $dataset->name }}'? This will also delete all rows."
                                            class="btn btn-sm btn-outline-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $datasets->links() }}
        </div>
    @endif
</div>
