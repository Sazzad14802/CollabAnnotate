<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Dashboard</h2>
    </x-slot>

    <div class="container-fluid py-4 px-4">

        {{-- Welcome Banner --}}
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name ?? 'User' }}</h4>
            <p class="text-muted mb-0">Here's a quick summary of your projects.</p>
        </div>

        @php
            $myProjectsCount = auth()->user()->ownedProjects()->count();
            $assignedProjectsCount = auth()->user()->assignedProjects()->count();
        @endphp

        <div class="row g-4">
            {{-- My Projects Card --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-4 text-primary">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">My Projects</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $myProjectsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Assigned Projects Card --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-4 text-info">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted fw-semibold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Assigned to me</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $assignedProjectsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
