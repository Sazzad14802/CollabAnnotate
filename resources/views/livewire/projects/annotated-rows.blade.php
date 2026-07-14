<div>
    <div class="card">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-semibold">Dataset Rows</h6>
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
                                                <div class="avatar avatar-sm bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center" style="width:24px; height:24px; font-size:10px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
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
