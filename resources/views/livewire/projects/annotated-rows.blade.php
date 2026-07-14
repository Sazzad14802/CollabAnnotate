<div>
    <div class="card">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-semibold">Annotated Rows</h6>
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
                        <th class="pe-4 text-end">Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedAssignments as $assignment)
                        @php
                            $data = $assignment->datasetRow->data ?? [];
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
                                    // Find the specific annotation for this field by this user on this row
                                    $annotation = $assignment->datasetRow->annotations->firstWhere(function ($ann) use ($field, $assignment) {
                                        return $ann->annotation_field_id === $field->id && $ann->user_id === $assignment->user_id;
                                    });
                                @endphp
                                <td class="bg-indigo-50 bg-opacity-25 fw-medium">
                                    {{ $annotation ? $annotation->value : '-' }}
                                </td>
                            @endforeach

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center" style="width:24px; height:24px; font-size:10px;">
                                        {{ strtoupper(substr($assignment->user->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $assignment->user->email }}</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                {{ $assignment->updated_at->format('M d, Y H:i') }}
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
        
        @if($completedAssignments->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $completedAssignments->links() }}
            </div>
        @endif
    </div>
</div>
