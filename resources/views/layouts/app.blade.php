<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} — CollabAnnotate</title>
    <meta name="description" content="Collaborative Dataset Annotation Platform for researchers, ML teams, and data analysts.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">

    @include('layouts.navigation')

            {{-- Toast --}}
            @if(session('success'))
                <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
                    <div id="successToast"
                        class="toast text-bg-success"
                        role="alert"
                        data-bs-delay="3000"
                        x-data
                        x-init="new bootstrap.Toast($el).show()">
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
                    <div id="errorToast"
                        class="toast text-bg-danger"
                        role="alert"
                        data-bs-delay="3000"
                        data-bs-autohide="true"
                        x-data
                        x-init="new bootstrap.Toast($el).show()">
                        <div class="toast-body">
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif
            @if(session('warning'))
                <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
                    <div id="warningToast"
                        class="toast text-bg-warning"
                        role="alert"
                        data-bs-delay="3000"
                        data-bs-autohide="true"
                        x-data
                        x-init="new bootstrap.Toast($el).show()">
                        <div class="toast-body">
                            {{ session('warning') }}
                        </div>
                    </div>
                </div>
            @endif

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

</div>
@livewireScripts
</body>
</html>
