<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: .06em;">Doctor</p>
                <h2 class="h3 mb-0">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, Dr. {{ Str::before(Auth::user()->name, ' ') }} 👋</h2>
                <p class="text-muted mb-0">Here's what's on your plate today.</p>
            </div>
            <a href="{{ route('doctor.appointments.index') }}" class="btn btn-primary">
                <i class="bi bi-calendar-check me-1"></i> All appointments
            </a>
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
                <div class="card card-stat" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
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
                    <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
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
                <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
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
                <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
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
                <strong><i class="bi bi-calendar-day me-2 text-primary"></i>Today's schedule &mdash; {{ today()->format('l, F j') }}</strong>
                <a href="{{ route('doctor.appointments.index') }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            @if($todayAppointments->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p class="text-muted mb-0">No appointments scheduled for today. Enjoy your day.</p>
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
                                    $patientInitial = strtoupper(substr($appt->patient->user->name, 0, 1));
                                @endphp
                                <tr>
                                    <td class="fw-semibold">
                                        <i class="bi bi-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="profile-avatar" style="width:2rem;height:2rem;font-size:.85rem;">{{ $patientInitial }}</span>
                                            <span class="fw-semibold">{{ $appt->patient->user->name }}</span>
                                        </div>
                                    </td>
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
