<div
    x-data="{
        activeRow: null,
        setActiveRow(rowId) {
            this.activeRow = rowId;
            $wire.setActiveRow(rowId);
        }
    }"
>
    {{-- Toolbar --}}
    {{-- Toolbar --}}
    <div class="d-flex mb-3">
        <button wire:click="fetchNext" class="btn btn-primary fw-medium px-4">Fetch Next Rows</button>
    </div>


    @error('ai_error')
        <div class="alert alert-danger d-flex align-items-center mb-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-2">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>{{ $message }}</div>
        </div>
    @enderror
    @if($rows->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h6 class="fw-semibold">No rows assigned to you yet</h6>
                <p class="text-muted small mb-0">Click "Fetch Next Rows" to get your first batch of data to annotate.</p>
            </div>
        </div>
    @else
        {{-- Annotation Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">#</th>
                        {{-- Dataset columns --}}
                        @foreach($project->column_names as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        {{-- Annotation columns --}}
                        @foreach($fields as $field)
                            <th class="table-primary">
                                {{ $field->name }}
                                <span class="text-danger">*</span>
                            </th>
                        @endforeach
                        <th style="width:100px;" class="text-center">AI Assist</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php 
                            $assignment = $row->rowAssignments->first();
                            $isComplete = $assignment && $assignment->status === 'completed'; 
                        @endphp
                        <tr wire:key="row-{{ $row->id }}"
                            @click="setActiveRow({{ $row->id }})"
                            style="cursor:pointer;"
                            class="{{ $isComplete ? 'table-success' : '' }} {{ $activeRowId === $row->id ? 'annotation-row-active' : '' }}">

                            <td class="text-muted small font-monospace">{{ $row->row_index + 1 }}</td>

                            @foreach($project->column_names as $col)
                                <td style="max-width:200px;">
                                    <div class="text-truncate small" title="{{ $row->data[$col] ?? '' }}">
                                        {{ $row->data[$col] ?? '—' }}
                                    </div>
                                </td>
                            @endforeach

                            @foreach($fields as $field)
                                <td class="table-primary bg-opacity-25" style="min-width:140px;" @click.stop>
                                    <select
                                        wire:change="saveAnnotation({{ $row->id }}, {{ $field->id }}, $event.target.value)"
                                        class="form-select form-select-sm"
                                        id="annot-{{ $row->id }}-{{ $field->id }}">
                                        <option value="">— Select —</option>
                                        @foreach($field->options ?? [] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ ($annotations[$row->id][$field->id] ?? null) === $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            @endforeach

                            <td class="text-center align-middle">
                                @if($isComplete)
                                    <svg class="text-success" width="20" height="20" fill="currentColor" viewBox="0 0 20 20" title="Completed">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <button 
                                        wire:click="suggestAnnotation({{ $row->id }})" 
                                        class="btn btn-sm btn-outline-primary py-0 px-2"
                                        title="Auto-fill with AI"
                                        wire:loading.attr="disabled"
                                        wire:target="suggestAnnotation({{ $row->id }})"
                                    >
                                        <span wire:loading.remove wire:target="suggestAnnotation({{ $row->id }})">🪄 Ask AI</span>
                                        <span wire:loading wire:target="suggestAnnotation({{ $row->id }})">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            {{ $rows->links() }}
        </div>
    @endif
</div>
