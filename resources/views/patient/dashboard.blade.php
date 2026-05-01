<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: .06em;">Patient</p>
                <h2 class="h3 mb-0">Welcome back, {{ Str::before(Auth::user()->name, ' ') }} 👋</h2>
                <p class="text-muted mb-0">Here's a quick look at your care.</p>
            </div>
            <a href="{{ route('patient.doctors.index') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Book appointment
            </a>
        </div>
    </x-slot>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('patient.appointments.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Upcoming</div>
                            <div class="stat-number">{{ $stats['upcoming'] }}</div>
                        </div>
                        <i class="bi bi-calendar-week stat-icon"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Completed</div>
                        <div class="stat-number">{{ $stats['completed'] }}</div>
                    </div>
                    <i class="bi bi-check2-circle stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('patient.records.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Records</div>
                            <div class="stat-number">{{ $stats['records'] }}</div>
                        </div>
                        <i class="bi bi-file-medical stat-icon"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('patient.prescriptions.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Prescriptions</div>
                            <div class="stat-number">{{ $stats['prescriptions'] }}</div>
                        </div>
                        <i class="bi bi-capsule stat-icon"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Profile snapshot --}}
    @if($patient)
        <div class="card mb-4 profile-snapshot">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="profile-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    <div class="flex-grow-1">
                        <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                        <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil me-1"></i> Edit profile
                    </a>
                </div>
                <div class="row text-center text-md-start g-2">
                    <div class="col-6 col-md-3">
                        <div class="profile-stat">
                            <i class="bi bi-cake2"></i>
                            <div>
                                <div class="text-muted small">Age</div>
                                <div class="fw-semibold">{{ $patient->date_of_birth->age }} yrs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="profile-stat">
                            <i class="bi bi-gender-ambiguous"></i>
                            <div>
                                <div class="text-muted small">Gender</div>
                                <div class="fw-semibold">{{ ucfirst($patient->gender) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="profile-stat">
                            <i class="bi bi-droplet-half"></i>
                            <div>
                                <div class="text-muted small">Blood group</div>
                                <div class="fw-semibold">{{ $patient->blood_group ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="profile-stat">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <div class="text-muted small">Phone</div>
                                <div class="fw-semibold">{{ Auth::user()->phone ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Your patient profile is incomplete. Please contact reception.
        </div>
    @endif

    {{-- Upcoming + records --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-calendar-week me-2 text-primary"></i>Upcoming appointments</strong>
                    @if($upcomingAppointments->isNotEmpty())
                        <a href="{{ route('patient.appointments.index') }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
                    @endif
                </div>
                @if($upcomingAppointments->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p class="text-muted mb-2">No upcoming appointments yet.</p>
                        <a href="{{ route('patient.doctors.index') }}" class="btn btn-sm btn-outline-primary">Browse doctors</a>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($upcomingAppointments as $appt)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="appt-date">
                                        <span class="appt-month">{{ $appt->appointment_date->format('M') }}</span>
                                        <span class="appt-day">{{ $appt->appointment_date->format('j') }}</span>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-truncate">Dr. {{ $appt->doctor->user->name }}</div>
                                        <div class="small text-muted text-truncate">
                                            {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                                            &middot; {{ $appt->doctor->department->name }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $appt->status === 'confirmed' ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                    <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-file-medical me-2 text-primary"></i>Recent medical records</strong>
                    @if($recentRecords->isNotEmpty())
                        <a href="{{ route('patient.records.index') }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
                    @endif
                </div>
                @if($recentRecords->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-file-earmark-medical"></i>
                        <p class="text-muted mb-0">No records yet.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentRecords as $rec)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-truncate">{{ Str::limit($rec->diagnosis, 50) }}</div>
                                        <div class="small text-muted">
                                            Dr. {{ $rec->appointment->doctor->user->name }} &middot;
                                            {{ $rec->appointment->appointment_date->format('M j, Y') }}
                                        </div>
                                    </div>
                                    <a href="{{ route('patient.records.show', $rec) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
