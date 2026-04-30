<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Appointment Details</h2>
            <a href="{{ route('patient.appointments.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Back to my appointments
            </a>
        </div>
    </x-slot>

    @php
        $colors = [
            'completed' => 'success',
            'cancelled' => 'secondary',
            'no_show'   => 'danger',
            'pending'   => 'warning',
            'confirmed' => 'info',
        ];
        $cancellable = ! in_array($appointment->status, ['cancelled', 'completed', 'no_show'])
                    && $appointment->appointment_date->isFuture();
    @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">Appointment with Dr. {{ $appointment->doctor->user->name }}</h5>
                        <span class="badge text-bg-{{ $colors[$appointment->status] ?? 'secondary' }} fs-6">
                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
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

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Department</div>
                            <div class="fw-semibold">{{ $appointment->doctor->department->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small">Specialization</div>
                            <div class="fw-semibold">{{ $appointment->doctor->specialization }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-muted text-uppercase small">Reason for visit</div>
                    <p class="mb-0">{{ $appointment->reason }}</p>
                </div>
            </div>

            @php $appointment->load('medicalRecord'); $record = $appointment->medicalRecord; @endphp
            @if($record)
                <div class="card mt-3">
                    <div class="card-header bg-white">
                        <strong><i class="bi bi-file-medical me-2"></i>Consultation Notes</strong>
                    </div>
                    <div class="card-body">
                        <h6 class="text-uppercase small text-muted">Diagnosis</h6>
                        <p>{{ $record->diagnosis }}</p>

                        @if($record->notes)
                            <h6 class="text-uppercase small text-muted mt-3">Notes from Dr. {{ $appointment->doctor->user->name }}</h6>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $record->notes }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Actions</h6>
                    @if($cancellable)
                        <form action="{{ route('patient.appointments.destroy', $appointment) }}" method="POST"
                              onsubmit="return confirm('Cancel this appointment? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i> Cancel Appointment
                            </button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">No actions available — this appointment is past or finalised.</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Consultation fee</h6>
                    <p class="text-primary fw-semibold mb-1">${{ number_format($appointment->doctor->consultation_fee, 2) }}</p>
                    <p class="text-muted small mb-0">
                        Payment will be collected at the appointment (online payment in Phase 13).
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
