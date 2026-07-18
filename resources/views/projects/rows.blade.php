@extends('projects.layout')

@section('project_content')
    <livewire:projects.annotated-rows :project="$project" />
@endsection
