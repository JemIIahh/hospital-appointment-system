<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">Prescription #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</h2>
                <a href="{{ route('patient.prescriptions.index') }}" class="text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Back to my prescriptions
                </a>
            </div>
            <a href="{{ route('patient.prescriptions.pdf', $prescription) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-lg-8">
            @if($prescription->general_instructions)
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong><i class="bi bi-info-circle me-2"></i>General Instructions</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $prescription->general_instructions }}</p>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-prescription2 me-2"></i>Medications</strong>
                    <span class="text-muted small">({{ $prescription->items->count() }} {{ Str::plural('item', $prescription->items->count()) }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescription->items as $item)
                                <tr>
                                    <td><strong>{{ $item->medication_name }}</strong></td>
                                    <td>{{ $item->dosage }}</td>
                                    <td>{{ $item->frequency }}</td>
                                    <td>{{ $item->duration }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <strong>Issued by</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <i class="bi bi-person-circle text-muted" style="font-size: 2.5rem;"></i>
                        <div>
                            <div class="fw-semibold">Dr. {{ $prescription->appointment->doctor->user->name }}</div>
                            <div class="text-muted small">{{ $prescription->appointment->doctor->specialization }}</div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="mb-2">
                        <div class="text-muted text-uppercase small">Department</div>
                        <div>{{ $prescription->appointment->doctor->department->name }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted text-uppercase small">Visit Date</div>
                        <div>{{ $prescription->appointment->appointment_date->format('M j, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase small">Issued</div>
                        <div>{{ $prescription->created_at->format('M j, Y \a\t g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
