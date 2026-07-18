@extends('projects.layout')

@section('project_content')
    <livewire:projects.annotator-manager :project="$project" />
@endsection
