<div class="row g-4">
    {{-- Search & Add --}}
    <div class="col-12 col-lg-4">
        <div class="card sticky-top" style="top:80px;">
            <div class="card-header fw-semibold">Add Annotator</div>
            <div class="card-body">
                <div class="position-relative">
                    <input wire:model.live.debounce.300ms="searchQuery"
                           type="text"
                           placeholder="Search by name or email..."
                           class="form-control"
                           id="annotator-search-input"
                           autocomplete="off">
                    <div wire:loading wire:target="updatedSearchQuery"
                         class="position-absolute top-50 end-0 translate-middle-y pe-3">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Searching...</span>
                        </div>
                    </div>
                </div>

                @if(!empty($searchResults))
                    <ul class="list-group mt-2">
                        @foreach($searchResults as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div style="min-width:0;" class="flex-fill me-2">
                                    <div class="fw-medium small text-truncate">{{ $user['name'] }}</div>
                                    <div class="text-muted small text-truncate">{{ $user['email'] }}</div>
                                </div>
                                <button wire:click="addAnnotator({{ $user['id'] }})"
                                        class="btn btn-primary btn-sm flex-shrink-0">Add</button>
                            </li>
                        @endforeach
                    </ul>
                @elseif(strlen($searchQuery) >= 2)
                    <p class="text-muted small mt-3 text-center mb-0">No users found.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Current Annotators --}}
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Current Annotators</span>
                <small class="text-muted">{{ $annotators->count() }} annotator(s)</small>
            </div>

            @if($annotators->isEmpty())
                <div class="card-body text-center py-5">
                    <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-muted mb-0">No annotators assigned yet.</p>
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($annotators as $annotator)
                        <li wire:key="annotator-{{ $annotator->id }}" class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 fw-semibold text-primary"
                                     style="width:36px;height:36px;font-size:14px;">
                                    {{ substr($annotator->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $annotator->name }}</div>
                                    <small class="text-muted">{{ $annotator->email }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <small class="text-muted">{{ number_format($annotator->annotation_count) }} annotations</small>
                                <button wire:click="removeAnnotator({{ $annotator->id }})"
                                        wire:confirm="Remove {{ $annotator->name }} from this project?"
                                        class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
