<div class="grid lg:grid-cols-3 gap-6">
    {{-- Search & Add --}}
    <div class="lg:col-span-1">
        <div class="card sticky top-24">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Add Annotator</h3>
            </div>
            <div class="card-body">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="searchQuery"
                           type="text"
                           placeholder="Search by name or email..."
                           class="form-input"
                           id="annotator-search-input"
                           autocomplete="off">
                    <div wire:loading wire:target="updatedSearchQuery"
                         class="absolute right-3 top-2.5">
                        <svg class="animate-spin w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                @if(!empty($searchResults))
                    <div class="mt-2 border border-gray-200 rounded-xl overflow-hidden">
                        @foreach($searchResults as $user)
                            <div class="flex items-center justify-between px-3 py-2.5 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $user['email'] }}</p>
                                </div>
                                <button wire:click="addAnnotator({{ $user['id'] }})"
                                        class="ml-2 btn-primary btn-sm shrink-0">Add</button>
                            </div>
                        @endforeach
                    </div>
                @elseif(strlen($searchQuery) >= 2)
                    <p class="text-sm text-gray-500 mt-3 text-center">No users found.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Current Annotators --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Current Annotators</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $annotators->count() }} annotator(s)</p>
            </div>

            @if($annotators->isEmpty())
                <div class="empty-state py-12">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No annotators assigned yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($annotators as $annotator)
                        <div wire:key="annotator-{{ $annotator->id }}" class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                                    {{ substr($annotator->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $annotator->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $annotator->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-gray-500">
                                    {{ number_format($annotator->annotation_count) }} annotations
                                </span>
                                <button wire:click="removeAnnotator({{ $annotator->id }})"
                                        wire:confirm="Remove {{ $annotator->name }} from this project?"
                                        class="btn-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1.5 rounded-lg transition-colors">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
