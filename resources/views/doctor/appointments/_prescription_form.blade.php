<form action="{{ $action }}" method="POST"
      x-data="{ items: {{ json_encode($items) }} }">
    @csrf
    @if($method === 'PATCH')
        @method('PATCH')
    @endif

    <div class="mb-3">
        <label for="general_instructions" class="form-label">
            General Instructions
            <span class="text-muted small">(optional, e.g. "Take with food, avoid alcohol")</span>
        </label>
        <textarea id="general_instructions" name="general_instructions" rows="2" maxlength="5000"
            class="form-control @error('general_instructions') is-invalid @enderror"
            placeholder="Lifestyle, dietary, or general medication advice for the patient">{{ old('general_instructions', $general) }}</textarea>
        @error('general_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <h6 class="text-uppercase small text-muted mt-4 mb-2">Medications</h6>

    <template x-for="(item, idx) in items" :key="idx">
        <div class="row g-2 mb-2 align-items-start">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Medication name"
                       :name="`items[${idx}][medication_name]`" x-model="item.medication_name" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Dosage"
                       :name="`items[${idx}][dosage]`" x-model="item.dosage" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Frequency"
                       :name="`items[${idx}][frequency]`" x-model="item.frequency" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Duration"
                       :name="`items[${idx}][duration]`" x-model="item.duration" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100"
                        x-show="items.length > 1" @click="items.splice(idx, 1)" x-cloak>
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>

    @error('items')<p class="text-danger small">{{ $message }}</p>@enderror
    @error('items.*')<p class="text-danger small">All medication fields are required.</p>@enderror

    <button type="button" class="btn btn-outline-secondary btn-sm mt-2"
            @click="items.push({ medication_name: '', dosage: '', frequency: '', duration: '' })">
        <i class="bi bi-plus-lg"></i> Add medication
    </button>

    <div class="mt-4 d-flex justify-content-end gap-2">
        @if($method === 'PATCH')
            <button type="button" class="btn btn-outline-secondary" x-on:click="$dispatch('toggle-edit-rx')">Cancel</button>
        @endif
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> {{ $method === 'PATCH' ? 'Update Prescription' : 'Save Prescription' }}
        </button>
    </div>
</form>
