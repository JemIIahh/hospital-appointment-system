<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">My Appointments</h2>
                <p class="text-muted small mb-0">Upcoming bookings and history</p>
            </div>
            <a href="{{ route('patient.doctors.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> New Appointment
            </a>
        </div>
    </x-slot>

    {{-- Upcoming --}}
    <h5 class="mb-3"><i class="bi bi-calendar-week me-2"></i>Upcoming</h5>

    @if($upcoming->isEmpty())
        <div class="card mb-4">
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p class="text-muted mb-2">No upcoming appointments.</p>
                <a href="{{ route('patient.doctors.index') }}" class="btn btn-sm btn-outline-primary">
                    Browse doctors to book
                </a>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($upcoming as $appt)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge {{ $appt->status === 'confirmed' ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                                <span class="text-muted small">{{ $appt->appointment_date->format('D, M j') }}</span>
                            </div>
                            <h6 class="mb-1">Dr. {{ $appt->doctor->user->name }}</h6>
                            <p class="text-muted small mb-2">
                                {{ $appt->doctor->department->name }} &middot;
                                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                            </p>
                            <p class="small text-truncate mb-3" title="{{ $appt->reason }}">
                                {{ Str::limit($appt->reason, 60) }}
                            </p>
                            <div class="d-flex gap-1">
                                <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                                    View
                                </a>
                                <form action="{{ route('patient.appointments.destroy', $appt) }}" method="POST"
                                      onsubmit="return confirm('Cancel this appointment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Historical --}}
    <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Past &amp; Cancelled</h5>

    @if($historical->isEmpty())
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-archive"></i>
                <p class="text-muted mb-0">No past appointments yet.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historical as $appt)
                            <tr>
                                <td>
                                    {{ $appt->appointment_date->format('M j, Y') }}<br>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</span>
                                </td>
                                <td>Dr. {{ $appt->doctor->user->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ $appt->doctor->department->name }}</span></td>
                                <td>
                                    @php
                                        $colors = [
                                            'completed' => 'success',
                                            'cancelled' => 'secondary',
                                            'no_show'   => 'danger',
                                            'pending'   => 'warning',
                                            'confirmed' => 'info',
                                        ];
                                    @endphp
                                    <span class="badge text-bg-{{ $colors[$appt->status] ?? 'secondary' }}">
                                        {{ str_replace('_', ' ', ucfirst($appt->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $historical->links() }}</div>
    @endif
</x-app-layout>
