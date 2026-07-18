@extends('projects.layout')

@section('project_content')
    @if($project->annotationFields()->count() === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h6 class="fw-semibold">No annotation schema defined yet</h6>
                <p class="text-muted small mt-1">
                    No annotation schema was defined for this project.
                </p>
            </div>
        </div>
    @elseif($project->import_status !== 'completed')
        <div class="alert alert-info">
            Dataset is still importing (status: {{ $project->import_status }}). Please wait.
        </div>
    @else
        <livewire:projects.annotation-workspace :project="$project" />
    @endif
@endsection
