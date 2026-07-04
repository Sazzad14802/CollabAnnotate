<x-app-layout>
    <x-slot name="pageTitle">{{ $project->name }}</x-slot>

    <div class="page-section max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ route('projects.index') }}" wire:navigate
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h2 class="text-xl font-bold text-gray-900">{{ $project->name }}</h2>
                    @if($project->status === 'active')
                        <span class="badge-green">Active</span>
                    @else
                        <span class="badge-gray">{{ ucfirst($project->status) }}</span>
                    @endif
                </div>
                @if($project->description)
                    <p class="text-sm text-gray-500 ml-8">{{ $project->description }}</p>
                @endif
            </div>

            {{-- Actions --}}
            @can('update', $project)
                <div class="flex items-center gap-2">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="btn-secondary btn-sm gap-2">
                            Export
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10">
                            <a href="{{ route('projects.export', ['project' => $project->id, 'format' => 'csv']) }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">CSV</a>
                            <a href="{{ route('projects.export', ['project' => $project->id, 'format' => 'xlsx']) }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">XLSX</a>
                        </div>
                    </div>
                    <a href="{{ route('projects.edit', $project) }}" wire:navigate class="btn-secondary btn-sm">Settings</a>
                </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert-success mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error mb-6">{{ session('error') }}</div>
        @endif

        {{-- Tabs --}}
        <div x-data="{ tab: '{{ request('tab', 'workspace') }}' }" class="space-y-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex gap-6 text-sm font-medium">
                    <button @click="tab = 'workspace'"
                            :class="tab === 'workspace' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                            class="pb-3 px-1 transition-colors">
                        Annotation Workspace
                    </button>
                    <button @click="tab = 'progress'"
                            :class="tab === 'progress' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                            class="pb-3 px-1 transition-colors">
                        Progress & Stats
                    </button>
                    @can('manageSchema', $project)
                        <button @click="tab = 'schema'"
                                :class="tab === 'schema' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                                class="pb-3 px-1 transition-colors">
                            Annotation Schema
                        </button>
                        <button @click="tab = 'annotators'"
                                :class="tab === 'annotators' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                                class="pb-3 px-1 transition-colors">
                            Annotators
                        </button>
                    @endcan
                    <button @click="tab = 'activity'"
                            :class="tab === 'activity' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                            class="pb-3 px-1 transition-colors">
                        Activity
                    </button>
                </nav>
            </div>

            {{-- Workspace Tab --}}
            <div x-show="tab === 'workspace'" x-cloak>
                @if($project->annotationFields()->count() === 0)
                    <div class="card">
                        <div class="empty-state py-12">
                            <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h4 class="text-gray-700 font-medium">No annotation schema defined yet</h4>
                            <p class="text-gray-500 text-sm mt-1">
                                @can('manageSchema', $project)
                                    Add annotation fields in the <button @click="tab = 'schema'" class="text-indigo-600 underline">Schema</button> tab first.
                                @else
                                    The project owner needs to define annotation fields first.
                                @endcan
                            </p>
                        </div>
                    </div>
                @elseif($project->dataset->import_status !== 'completed')
                    <div class="alert-info">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Dataset is still importing (status: {{ $project->dataset->import_status }}). Please wait.
                    </div>
                @else
                    <livewire:projects.annotation-workspace :project="$project" />
                @endif
            </div>

            {{-- Progress Tab --}}
            <div x-show="tab === 'progress'" x-cloak>
                <livewire:projects.progress-tracker :project="$project" />
            </div>

            {{-- Schema Tab --}}
            @can('manageSchema', $project)
                <div x-show="tab === 'schema'" x-cloak>
                    <livewire:projects.schema-builder :project="$project" />
                </div>

                {{-- Annotators Tab --}}
                <div x-show="tab === 'annotators'" x-cloak>
                    <livewire:projects.annotator-manager :project="$project" />
                </div>
            @endcan

            {{-- Activity Tab --}}
            <div x-show="tab === 'activity'" x-cloak>
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Recent Activity</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentActivity as $log)
                            <div class="px-6 py-3 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="text-indigo-600 text-xs font-bold">{{ substr($log->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-800">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $log->user->name }} · {{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm text-gray-500">No activity yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
