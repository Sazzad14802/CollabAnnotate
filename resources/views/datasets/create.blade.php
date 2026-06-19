<x-app-layout>
    <x-slot name="pageTitle">Upload Dataset</x-slot>

    <div class="page-section max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('datasets.index') }}" wire:navigate
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Datasets
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Upload Dataset</h2>
                <p class="text-sm text-gray-500 mt-1">Upload a CSV or XLSX file. Columns will be auto-detected.</p>
            </div>

            <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body space-y-5">
                    <div>
                        <label for="ds-name" class="form-label">Dataset Name <span class="text-red-500">*</span></label>
                        <input type="text" id="ds-name" name="name" value="{{ old('name') }}"
                               class="form-input" placeholder="e.g. Product Reviews Q1 2024" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">File <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-200 border-dashed rounded-xl hover:border-indigo-400 transition-colors"
                             x-data="{ fileName: '' }"
                             @dragover.prevent
                             @drop.prevent="
                                 let f = $event.dataTransfer.files[0];
                                 fileName = f ? f.name : '';
                                 $refs.fileInput.files = $event.dataTransfer.files;
                             ">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <div class="mt-3">
                                    <label for="ds-file" class="cursor-pointer">
                                        <span class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">Choose file</span>
                                        <span class="text-gray-500 text-sm"> or drag and drop</span>
                                    </label>
                                    <input id="ds-file" name="file" type="file" accept=".csv,.xlsx"
                                           x-ref="fileInput"
                                           @change="fileName = $event.target.files[0]?.name || ''"
                                           class="sr-only">
                                </div>
                                <p x-text="fileName || 'CSV or XLSX up to 50MB'" class="text-xs text-gray-400 mt-1"></p>
                            </div>
                        </div>
                        @error('file') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('datasets.index') }}" wire:navigate class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Upload & Import</button>
                </div>
            </form>
        </div>

        {{-- Format Guide --}}
        <div class="card mt-6 bg-blue-50 border-blue-200">
            <div class="card-body">
                <h3 class="font-semibold text-blue-900 mb-2">Expected Format</h3>
                <p class="text-sm text-blue-800 mb-3">Your file should have headers in the first row:</p>
                <div class="bg-white rounded-lg p-3 font-mono text-xs text-gray-700 overflow-x-auto">
                    <div class="font-bold text-gray-500 border-b pb-1 mb-1">id,text,source</div>
                    <div>1,I love this product,Twitter</div>
                    <div>2,Delivery was slow,Reddit</div>
                    <div>3,Great customer service,Facebook</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
