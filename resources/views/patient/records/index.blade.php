<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">My Medical Records</h2>
            <p class="text-muted small mb-0">Diagnoses and consultation notes from your appointments</p>
        </div>
    </x-slot>

    @if($records->isEmpty())
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-file-earmark-medical"></i>
                <p class="text-muted mb-2">No medical records yet.</p>
                <p class="text-muted small mb-0">Records appear here after a doctor writes up your consultation.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date of visit</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Diagnosis</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>{{ $record->appointment->appointment_date->format('M j, Y') }}</td>
                                <td>Dr. {{ $record->appointment->doctor->user->name }}</td>
                                <td>
                                    <span class="badge text-bg-secondary">{{ $record->appointment->doctor->department->name }}</span>
                                </td>
                                <td class="text-muted small">{{ Str::limit($record->diagnosis, 80) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('patient.records.show', $record) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $records->links() }}</div>
    @endif
</x-app-layout>
