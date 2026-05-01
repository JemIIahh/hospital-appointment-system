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
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0"><i class="bi bi-calendar-week me-2 text-primary"></i>Upcoming</h5>
        @if($upcoming->isNotEmpty())
            <span class="badge text-bg-light border">{{ $upcoming->count() }} {{ Str::plural('appointment', $upcoming->count()) }}</span>
        @endif
    </div>

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
                    <div class="card h-100 appt-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="appt-date appt-date-lg">
                                    <span class="appt-month">{{ $appt->appointment_date->format('M') }}</span>
                                    <span class="appt-day">{{ $appt->appointment_date->format('j') }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fw-semibold text-truncate">Dr. {{ $appt->doctor->user->name }}</h6>
                                    <p class="text-muted small mb-1 text-truncate">{{ $appt->doctor->department->name }}</p>
                                    <span class="badge {{ $appt->status === 'confirmed' ? 'text-bg-success' : 'text-bg-warning' }}">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }} &middot; {{ ucfirst($appt->status) }}
                                    </span>
                                </div>
                            </div>
                            <p class="small text-muted mb-3" title="{{ $appt->reason }}">
                                <i class="bi bi-chat-left-text me-1"></i>{{ Str::limit($appt->reason, 70) }}
                            </p>
                            <div class="d-flex gap-1">
                                <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                                    View details
                                </a>
                                <form action="{{ route('patient.appointments.destroy', $appt) }}" method="POST"
                                      onsubmit="return confirm('Cancel this appointment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel appointment">
                                        <i class="bi bi-x-lg"></i>
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
