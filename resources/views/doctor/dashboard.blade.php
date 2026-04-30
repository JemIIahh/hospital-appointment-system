<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">Doctor Dashboard</h2>
                <p class="text-muted small mb-0">Today's schedule and pending confirmations</p>
            </div>
            <span class="badge text-bg-success">Dr. {{ Auth::user()->name }}</span>
        </div>
    </x-slot>

    @if(! $doctor)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Your doctor profile is incomplete. Please contact the administrator.
        </div>
    @else
        {{-- Stat cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card card-stat" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Today</div>
                            <div class="stat-number">{{ $stats['today'] }}</div>
                        </div>
                        <i class="bi bi-calendar-day stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('doctor.appointments.index') }}?status=pending" class="text-decoration-none">
                    <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Pending</div>
                                <div class="stat-number">{{ $stats['pending'] }}</div>
                            </div>
                            <i class="bi bi-clock-history stat-icon"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">This Week</div>
                            <div class="stat-number">{{ $stats['thisWeek'] }}</div>
                        </div>
                        <i class="bi bi-calendar-week stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Completed</div>
                            <div class="stat-number">{{ $stats['totalCompleted'] }}</div>
                        </div>
                        <i class="bi bi-check2-circle stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's schedule --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-calendar-day me-2"></i>Today's Schedule &mdash; {{ today()->format('l, F j') }}</strong>
                <a href="{{ route('doctor.appointments.index') }}" class="small">View all appointments</a>
            </div>
            @if($todayAppointments->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p class="text-muted mb-0">No appointments scheduled for today.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAppointments as $appt)
                                @php
                                    $colors = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','no_show'=>'danger','cancelled'=>'secondary'];
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                                    <td>{{ $appt->patient->user->name }}</td>
                                    <td class="text-muted small">{{ Str::limit($appt->reason, 60) }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $colors[$appt->status] ?? 'secondary' }}">
                                            {{ str_replace('_',' ',ucfirst($appt->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
