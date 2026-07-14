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
                                @if($field->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </th>
                        @endforeach
                        <th style="width:60px;" class="text-center">Done</th>
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
                                    @if($field->type === 'select')
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
                                    @elseif($field->type === 'checkbox')
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                wire:change="saveAnnotation({{ $row->id }}, {{ $field->id }}, $event.target.checked ? '1' : '0')"
                                                {{ ($annotations[$row->id][$field->id] ?? '0') === '1' ? 'checked' : '' }}
                                                id="annot-{{ $row->id }}-{{ $field->id }}">
                                            <label class="form-check-label small" for="annot-{{ $row->id }}-{{ $field->id }}">Yes</label>
                                        </div>
                                    @endif
                                </td>
                            @endforeach

                            <td class="text-center">
                                @if($isComplete)
                                    <svg class="text-success" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <div class="rounded-circle border border-2 mx-auto" style="width:16px;height:16px;"></div>
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
