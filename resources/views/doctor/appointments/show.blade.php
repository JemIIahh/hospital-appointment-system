<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Appointment Details</h2>
            <a href="{{ route('doctor.appointments.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Back to my appointments
            </a>
        </div>
    </x-slot>

    @php
        $colors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','no_show'=>'danger','cancelled'=>'secondary'];
        $labels = ['confirmed'=>'Confirm','completed'=>'Mark Completed','cancelled'=>'Cancel','no_show'=>'Mark No-Show'];
        $btnClasses = ['confirmed'=>'btn-success','completed'=>'btn-primary','cancelled'=>'btn-outline-danger','no_show'=>'btn-outline-danger'];
        $btnIcons = ['confirmed'=>'bi-check-circle','completed'=>'bi-check2-all','cancelled'=>'bi-x-circle','no_show'=>'bi-person-x'];
    @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">Appointment with {{ $appointment->patient->user->name }}</h5>
                        <span class="badge text-bg-{{ $colors[$appointment->status] ?? 'secondary' }} fs-6">
                            {{ str_replace('_',' ',ucfirst($appointment->status)) }}
                        </span>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Date</div>
                            <div class="fw-semibold">{{ $appointment->appointment_date->format('l, F j, Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Time</div>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-muted text-uppercase small">Reason for visit</div>
                    <p class="mb-0">{{ $appointment->reason }}</p>
                </div>
            </div>

            {{-- Patient info card --}}
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-person-circle me-2"></i>Patient Information</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Name</div>
                            <div class="fw-semibold">{{ $appointment->patient->user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Email</div>
                            <div>{{ $appointment->patient->user->email }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted text-uppercase small">Age</div>
                            <div class="fw-semibold">{{ $appointment->patient->date_of_birth->age }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted text-uppercase small">Gender</div>
                            <div class="fw-semibold">{{ ucfirst($appointment->patient->gender) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted text-uppercase small">Blood Group</div>
                            <div class="fw-semibold">{{ $appointment->patient->blood_group }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted text-uppercase small">Phone</div>
                            <div class="fw-semibold">{{ $appointment->patient->user->phone ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Update Status</h6>

                    @if(empty($allowedTransitions))
                        <p class="text-muted small mb-0">
                            This appointment is in a final state ({{ str_replace('_',' ',$appointment->status) }})
                            and can no longer be changed.
                        </p>
                    @else
                        <div class="d-flex flex-column gap-2">
                            @foreach($allowedTransitions as $transition)
                                <form action="{{ route('doctor.appointments.update-status', $appointment) }}" method="POST"
                                      onsubmit="return confirm('Mark this appointment as {{ str_replace('_',' ',$transition) }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $transition }}">
                                    <button type="submit" class="btn {{ $btnClasses[$transition] ?? 'btn-outline-secondary' }} w-100">
                                        <i class="bi {{ $btnIcons[$transition] ?? 'bi-arrow-right' }} me-1"></i>
                                        {{ $labels[$transition] ?? ucfirst($transition) }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Prescription</h6>
                    <button class="btn btn-outline-secondary w-100" disabled>
                        <i class="bi bi-prescription2 me-1"></i> Add Prescription
                    </button>
                    <p class="text-muted small mt-2 mb-0">
                        Prescriptions and PDF generation arrive in Phase 11.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Consultation notes section (full-width below) --}}
    @php
        $writable = in_array($appointment->status, ['confirmed', 'completed'], true);
        $appointment->load('medicalRecord');
        $record = $appointment->medicalRecord;
    @endphp

    <div class="card mt-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-file-medical me-2"></i>Consultation Notes</strong>
            @if($record && $writable)
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        x-data x-on:click="$dispatch('toggle-edit-notes')">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            @endif
        </div>
        <div class="card-body" x-data="{ editing: {{ $record ? 'false' : 'true' }} }"
             x-on:toggle-edit-notes.window="editing = !editing">

            @if(! $writable && ! $record)
                <p class="text-muted mb-0">
                    Notes can only be added for confirmed or completed appointments.
                    Current status: <span class="badge text-bg-secondary">{{ $appointment->status }}</span>
                </p>

            @elseif($record)
                {{-- Read view --}}
                <div x-show="!editing">
                    <h6 class="text-uppercase small text-muted">Diagnosis</h6>
                    <p>{{ $record->diagnosis }}</p>

                    @if($record->notes)
                        <h6 class="text-uppercase small text-muted mt-3">Notes</h6>
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $record->notes }}</p>
                    @endif

                    <p class="text-muted small mt-3 mb-0">
                        Last updated {{ $record->updated_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Edit form --}}
                <div x-show="editing" style="display:none">
                    <form action="{{ route('doctor.appointments.notes.update', $appointment) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                            <textarea id="diagnosis" name="diagnosis" rows="2" required maxlength="5000"
                                class="form-control @error('diagnosis') is-invalid @enderror">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                            @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" rows="5" maxlength="10000"
                                class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" x-on:click="editing = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>

            @else
                {{-- Create form --}}
                <form action="{{ route('doctor.appointments.notes.store', $appointment) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                        <textarea id="diagnosis" name="diagnosis" rows="2" required maxlength="5000"
                            placeholder="Primary diagnosis, e.g. Hypertension stage 2"
                            class="form-control @error('diagnosis') is-invalid @enderror">{{ old('diagnosis') }}</textarea>
                        @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" rows="5" maxlength="10000"
                            placeholder="Detailed observations, recommended follow-up, lifestyle advice, etc."
                            class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Consultation Notes
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
