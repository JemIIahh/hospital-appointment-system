<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">My Appointments</h2>
            <p class="text-muted small mb-0">All bookings made with you</p>
        </div>
    </x-slot>

    @php
        $colors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','no_show'=>'danger','cancelled'=>'secondary'];
    @endphp

    {{-- Upcoming --}}
    <h5 class="mb-3"><i class="bi bi-calendar-week me-2"></i>Upcoming</h5>

    @if($upcoming->isEmpty())
        <div class="card mb-4">
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p class="text-muted mb-0">No upcoming appointments.</p>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcoming as $appt)
                            <tr>
                                <td>{{ $appt->appointment_date->format('M j, Y') }}</td>
                                <td class="fw-semibold">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                                <td>{{ $appt->patient->user->name }}</td>
                                <td class="text-muted small">{{ Str::limit($appt->reason, 60) }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $colors[$appt->status] ?? 'secondary' }}">
                                        {{ str_replace('_',' ',ucfirst($appt->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historical as $appt)
                            <tr>
                                <td>{{ $appt->appointment_date->format('M j, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                                <td>{{ $appt->patient->user->name }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $colors[$appt->status] ?? 'secondary' }}">
                                        {{ str_replace('_',' ',ucfirst($appt->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
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
