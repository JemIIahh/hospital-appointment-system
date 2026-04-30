@csrf

<h6 class="text-muted text-uppercase small mb-3">Personal Information</h6>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input id="name" name="name" type="text"
            value="{{ old('name', $doctor->user->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror" required autofocus>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input id="email" name="email" type="email"
            value="{{ old('email', $doctor->user->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input id="phone" name="phone" type="text"
            value="{{ old('phone', $doctor->user->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<h6 class="text-muted text-uppercase small mb-3">Professional Details</h6>

<div class="row g-3">
    <div class="col-md-6">
        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
        <select id="department_id" name="department_id"
            class="form-select @error('department_id') is-invalid @enderror" required>
            <option value="">— Select department —</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}"
                    @selected(old('department_id', $doctor->department_id ?? '') == $dept->id)>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
        <input id="specialization" name="specialization" type="text"
            value="{{ old('specialization', $doctor->specialization ?? '') }}"
            class="form-control @error('specialization') is-invalid @enderror"
            placeholder="e.g. Pediatric Cardiologist" required>
        @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
        <input id="license_number" name="license_number" type="text"
            value="{{ old('license_number', $doctor->license_number ?? '') }}"
            class="form-control @error('license_number') is-invalid @enderror"
            placeholder="e.g. MED-123456" required>
        @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="consultation_fee" class="form-label">Consultation Fee <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input id="consultation_fee" name="consultation_fee" type="number" step="0.01" min="0"
                value="{{ old('consultation_fee', $doctor->consultation_fee ?? '') }}"
                class="form-control @error('consultation_fee') is-invalid @enderror" required>
            @error('consultation_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <label for="bio" class="form-label">Bio</label>
        <textarea id="bio" name="bio" rows="3"
            class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $doctor->bio ?? '') }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
