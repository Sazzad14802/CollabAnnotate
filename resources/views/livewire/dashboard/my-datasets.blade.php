<div>
    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Search datasets..."
               class="form-input max-w-xs"
               id="dataset-search-input">
    </div>

    @if($datasets->isEmpty())
        <div class="card">
            <div class="empty-state py-10">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <p class="text-gray-500 text-sm">No datasets found. Upload your first dataset.</p>
            </div>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Dataset Name</th>
                        <th>Rows</th>
                        <th>Columns</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datasets as $dataset)
                        <tr wire:key="dataset-{{ $dataset->id }}">
                            <td>
                                <div class="font-medium text-gray-900">{{ $dataset->name }}</div>
                                <div class="text-xs text-gray-400">{{ $dataset->original_filename }}</div>
                            </td>
                            <td>{{ number_format($dataset->row_count) }}</td>
                            <td>{{ count($dataset->column_names) }}</td>
                            <td>
                                @if($dataset->import_status === 'completed')
                                    <span class="badge-green">Imported</span>
                                @elseif($dataset->import_status === 'processing')
                                    <span class="badge-yellow">Processing...</span>
                                @elseif($dataset->import_status === 'pending')
                                    <span class="badge-gray">Pending</span>
                                @else
                                    <span class="badge-red">Failed</span>
                                @endif
                            </td>
                            <td class="text-gray-500">{{ $dataset->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('datasets.show', $dataset) }}" wire:navigate
                                       class="btn-ghost btn-sm">View</a>
                                    <a href="{{ route('datasets.edit', $dataset) }}" wire:navigate
                                       class="btn-ghost btn-sm">Edit</a>
                                    <button wire:click="deleteDataset({{ $dataset->id }})"
                                            wire:confirm="Delete dataset '{{ $dataset->name }}'? This will also delete all rows."
                                            class="btn-sm text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg px-2 py-1.5 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $datasets->links() }}
        </div>
    @endif
</div>
