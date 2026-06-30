<div class="grid lg:grid-cols-3 gap-6">
    {{-- Add Field Form --}}
    <div class="lg:col-span-1">
        <div class="card sticky top-24">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">
                    {{ $editing ? 'Edit Field' : 'Add Annotation Field' }}
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Field Name <span class="text-red-500">*</span></label>
                    <input wire:model="fieldName" type="text" class="form-input"
                           placeholder="e.g. Sentiment" id="field-name-input">
                    @error('fieldName') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Field Type</label>
                    <select wire:model.live="fieldType" class="form-select" id="field-type-select">
                        <option value="select">Select (dropdown)</option>
                        <option value="checkbox">Checkbox (yes/no)</option>
                    </select>
                </div>

                @if($fieldType === 'select')
                    <div>
                        <label class="form-label">Options <span class="text-red-500">*</span></label>
                        <input wire:model="optionsInput" type="text" class="form-input"
                               placeholder="Positive, Neutral, Negative"
                               id="options-input">
                        <p class="text-xs text-gray-500 mt-1">Separate options with commas.</p>
                        @error('optionsInput') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <input wire:model="isRequired" type="checkbox" id="is-required" class="rounded text-indigo-600">
                    <label for="is-required" class="text-sm text-gray-700">Required field</label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button wire:click="saveField" class="btn-primary flex-1">
                        {{ $editing ? 'Update Field' : 'Add Field' }}
                    </button>
                    @if($editing)
                        <button wire:click="cancelEdit" class="btn-secondary">Cancel</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Field List --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Annotation Fields</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $fields->count() }} field(s) defined</p>
            </div>

            @if($fields->isEmpty())
                <div class="empty-state py-12">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No fields yet. Add your first annotation field.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($fields as $field)
                        <div wire:key="field-{{ $field->id }}" class="px-6 py-4 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $field->name }}</span>
                                    <span class="badge-{{ $field->type === 'select' ? 'indigo' : 'yellow' }}">
                                        {{ ucfirst($field->type) }}
                                    </span>
                                    @if($field->is_required)
                                        <span class="badge-red">Required</span>
                                    @endif
                                </div>
                                @if($field->type === 'select' && $field->options)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($field->options as $opt)
                                            <span class="badge-gray">{{ $opt }}</span>
                                        @endforeach
                                    </div>
                                @elseif($field->type === 'checkbox')
                                    <p class="text-xs text-gray-500 mt-1">Checked / Unchecked</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-4 shrink-0">
                                <button wire:click="editField({{ $field->id }})"
                                        class="btn-ghost btn-sm">Edit</button>
                                <button wire:click="deleteField({{ $field->id }})"
                                        wire:confirm="Delete field '{{ $field->name }}'? This will also delete all its annotations."
                                        class="text-red-500 hover:text-red-700 btn-sm px-2 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
