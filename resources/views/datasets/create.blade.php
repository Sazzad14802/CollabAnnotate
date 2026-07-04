<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('datasets.index') }}" wire:navigate class="text-muted small d-inline-flex align-items-center gap-1">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Datasets
        </a>
    </x-slot>

    <div class="container py-4" style="max-width:600px;">
        <div class="mb-3">
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <p class="text-muted small mb-0">Upload a CSV or XLSX file. Columns will be auto-detected.</p>
            </div>

            <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="ds-name" class="form-label">Dataset Name <span class="text-danger">*</span></label>
                        <input type="text" id="ds-name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Product Reviews Q1 2024" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <div class="border border-2 border-dashed rounded text-center p-4"
                             style="border-color:#dee2e6;"
                             x-data="{ fileName: '' }"
                             @dragover.prevent
                             @drop.prevent="
                                 let f = $event.dataTransfer.files[0];
                                 fileName = f ? f.name : '';
                                 $refs.fileInput.files = $event.dataTransfer.files;
                             ">
                            <svg class="mb-2 text-muted" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div>
                                <label for="ds-file" class="text-primary" style="cursor:pointer;">Choose file</label>
                                <span class="text-muted"> or drag and drop</span>
                            </div>
                            <input id="ds-file" name="file" type="file" accept=".csv,.xlsx"
                                   x-ref="fileInput"
                                   @change="fileName = $event.target.files[0]?.name || ''"
                                   class="d-none">
                            <p x-text="fileName || 'CSV or XLSX up to 50MB'" class="text-muted small mt-1 mb-0"></p>
                        </div>
                        @error('file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('datasets.index') }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Upload &amp; Import</button>
                </div>
            </form>
        </div>

        {{-- Format Guide --}}
        <div class="card border-info-subtle bg-info-subtle">
            <div class="card-body">
                <h6 class="fw-semibold text-info-emphasis mb-2">Expected Format</h6>
                <p class="small text-info-emphasis mb-2">Your file should have headers in the first row:</p>
                <div class="bg-white rounded p-2 font-monospace small">
                    <div class="fw-bold text-muted border-bottom pb-1 mb-1">id,text,source</div>
                    <div>1,I love this product,Twitter</div>
                    <div>2,Delivery was slow,Reddit</div>
                    <div>3,Great customer service,Facebook</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
