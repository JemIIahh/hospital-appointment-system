<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Medical Record</h2>
            <a href="{{ route('patient.records.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Back to my records
            </a>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-file-medical me-2"></i>Consultation Notes</strong>
                </div>
                <div class="card-body">
                    <h6 class="text-uppercase small text-muted">Diagnosis</h6>
                    <p>{{ $record->diagnosis }}</p>

                    @if($record->notes)
                        <h6 class="text-uppercase small text-muted mt-3">Notes</h6>
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $record->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <strong>Visit Details</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted text-uppercase small">Doctor</div>
                        <div class="fw-semibold">Dr. {{ $record->appointment->doctor->user->name }}</div>
                        <div class="text-muted small">{{ $record->appointment->doctor->specialization }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted text-uppercase small">Department</div>
                        <div class="fw-semibold">{{ $record->appointment->doctor->department->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted text-uppercase small">Date of Visit</div>
                        <div class="fw-semibold">{{ $record->appointment->appointment_date->format('l, F j, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase small">Reason for Visit</div>
                        <p class="mb-0">{{ $record->appointment->reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
