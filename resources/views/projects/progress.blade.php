@extends('projects.layout')

@section('project_content')
    <livewire:projects.progress-tracker :project="$project" />
@endsection
