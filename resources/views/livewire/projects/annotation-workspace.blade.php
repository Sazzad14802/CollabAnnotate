<div
    x-data="{
        activeRow: null,
        setActiveRow(rowId) {
            this.activeRow = rowId;
            $wire.setActiveRow(rowId);
        }
    }"
    @keydown.tab.prevent="
        // Tab navigation between rows will be handled by browser's natural tab order
    "
>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        {{-- Search --}}
        <div class="flex-1">
            <input wire:model.live.debounce.400ms="search"
                   type="text"
                   placeholder="Search rows..."
                   class="form-input"
                   id="workspace-search">
        </div>

        {{-- Filter --}}
        <div class="flex gap-2">
            @foreach(['all' => 'All', 'unannotated' => 'Unannotated', 'annotated' => 'Annotated'] as $value => $label)
                <button wire:click="$set('filterStatus', '{{ $value }}')"
                        class="btn-sm rounded-lg px-3 py-1.5 font-medium transition-colors
                               {{ $filterStatus === $value ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Loading overlay --}}
    <div wire:loading wire:target="search,filterStatus,saveAnnotation"
         class="fixed inset-0 bg-white/20 z-10 flex items-center justify-center pointer-events-none">
        <div class="bg-white rounded-full shadow-lg p-3">
            <svg class="animate-spin w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="card">
            <div class="empty-state py-12">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h4 class="text-gray-700 font-medium">No rows assigned to you yet</h4>
                <p class="text-gray-500 text-sm mt-1">Rows will be assigned automatically when you open this workspace.</p>
            </div>
        </div>
    @else
        {{-- Annotation Table --}}
        <div class="card overflow-x-auto">
            <table class="data-table min-w-full">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        {{-- Dataset columns --}}
                        @foreach($project->dataset->column_names as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        {{-- Annotation columns --}}
                        @foreach($fields as $field)
                            <th class="bg-indigo-50">
                                <div class="flex items-center gap-1">
                                    {{ $field->name }}
                                    @if($field->is_required)
                                        <span class="text-red-400 text-xs">*</span>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="w-16 text-center">Done</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php
                            $isComplete = $row->status === 'completed';
                        @endphp
                        <tr wire:key="row-{{ $row->id }}"
                            @click="setActiveRow({{ $row->id }})"
                            class="cursor-pointer transition-colors
                                   {{ $isComplete ? 'bg-green-50/50' : '' }}
                                   {{ $activeRowId === $row->id ? '!bg-indigo-50/80 ring-1 ring-inset ring-indigo-200' : '' }}">

                            {{-- Row number --}}
                            <td class="text-gray-400 text-xs font-mono">{{ $row->row_index + 1 }}</td>

                            {{-- Original data --}}
                            @foreach($project->dataset->column_names as $col)
                                <td class="max-w-xs">
                                    <div class="truncate text-sm" title="{{ $row->data[$col] ?? '' }}">
                                        {{ $row->data[$col] ?? '—' }}
                                    </div>
                                </td>
                            @endforeach

                            {{-- Annotation inputs --}}
                            @foreach($fields as $field)
                                <td class="bg-indigo-50/30 min-w-[140px]" @click.stop>
                                    @if($field->type === 'select')
                                        <select
                                            wire:change="saveAnnotation({{ $row->id }}, {{ $field->id }}, $event.target.value)"
                                            class="form-select text-xs py-1 px-2 border-gray-200 bg-white"
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
                                        <label class="flex items-center gap-2 cursor-pointer px-2">
                                            <input
                                                type="checkbox"
                                                wire:change="saveAnnotation({{ $row->id }}, {{ $field->id }}, $event.target.checked ? '1' : '0')"
                                                {{ ($annotations[$row->id][$field->id] ?? '0') === '1' ? 'checked' : '' }}
                                                class="rounded text-indigo-600 focus:ring-indigo-500"
                                                id="annot-{{ $row->id }}-{{ $field->id }}">
                                            <span class="text-xs text-gray-600">Yes</span>
                                        </label>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Completion indicator --}}
                            <td class="text-center">
                                @if($isComplete)
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 mx-auto"></div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination + Stats --}}
        <div class="flex items-center justify-between mt-4">
            <p class="text-sm text-gray-500">
                Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }} assigned rows
            </p>
            {{ $rows->links() }}
        </div>
    @endif
</div>
