<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">My Prescriptions</h2>
            <p class="text-muted small mb-0">Medications prescribed by your doctors</p>
        </div>
    </x-slot>

    @if($prescriptions->isEmpty())
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-prescription2"></i>
                <p class="text-muted mb-2">No prescriptions yet.</p>
                <p class="text-muted small mb-0">Prescriptions appear here after a doctor writes one for you.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Issued</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th class="text-center">Medications</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $rx)
                            <tr>
                                <td>
                                    {{ $rx->created_at->format('M j, Y') }}<br>
                                    <span class="text-muted small">#{{ str_pad($rx->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>Dr. {{ $rx->appointment->doctor->user->name }}</td>
                                <td>
                                    <span class="badge text-bg-secondary">{{ $rx->appointment->doctor->department->name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge text-bg-info">{{ $rx->items->count() }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('patient.prescriptions.show', $rx) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                    <a href="{{ route('patient.prescriptions.pdf', $rx) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $prescriptions->links() }}</div>
    @endif
</x-app-layout>
