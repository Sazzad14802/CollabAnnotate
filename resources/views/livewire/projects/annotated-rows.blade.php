<div wire:poll.5s>
    <div class="card">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">Dataset Rows</h6>
            <div style="width: 250px;">
                <input type="text" wire:model.live.debounce.300ms="annotatorEmail" class="form-control form-control-sm" placeholder="Filter by annotator email...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="min-width: max-content;">
                <thead class="table-light">
                    <tr>
                        {{-- Dataset Original Columns --}}
                        @if($project->column_names)
                                @foreach($project->column_names as $colName)
                                <th>{{ $colName }}</th>
                            @endforeach
                        @endif

                        {{-- Annotation Fields --}}
                        @foreach($project->annotationFields as $field)
                            <th class="bg-indigo-50 text-indigo">{{ $field->name }}</th>
                        @endforeach

                        <th>Annotator Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datasetRows as $row)
                        @php
                            $data = $row->data ?? [];
                            $annotators = $row->annotations->pluck('user')->unique('id');
                        @endphp
                        <tr>
                            
                            {{-- Dataset Original Values --}}
                            @if($project->column_names)
                                    @foreach($project->column_names as $colName)
                                    <td class="text-truncate" style="max-width: 250px;" title="{{ $data[$colName] ?? '' }}">
                                        {{ $data[$colName] ?? '-' }}
                                    </td>
                                @endforeach
                            @endif

                            {{-- Annotation Values --}}
                            @foreach($project->annotationFields as $field)
                                @php
                                    // Just get the first annotation value for this field on this row
                                    $annotation = $row->annotations->firstWhere('annotation_field_id', $field->id);
                                @endphp
                                <td class="bg-indigo-50 bg-opacity-25 fw-medium">
                                    {{ $annotation ? $annotation->value : '-' }}
                                </td>
                            @endforeach

                            <td>
                                @if($annotators->isNotEmpty())
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($annotators as $user)
                                            <div class="d-flex align-items-center gap-2">
                                                <span>{{ $user->email }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center py-5 text-muted">
                                No rows have been annotated yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        

    </div>
</div>
