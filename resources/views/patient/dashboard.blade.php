<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">Patient Dashboard</h2>
                <p class="text-muted small mb-0">Your appointments, records, and bookings</p>
            </div>
            <span class="badge text-bg-info">{{ Auth::user()->name }}</span>
        </div>
    </x-slot>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Welcome back, {{ Auth::user()->name }}</h5>
                    @if($patient)
                        <div class="row text-center text-md-start">
                            <div class="col-6 col-md-3 mb-2">
                                <div class="text-muted small">Age</div>
                                <div class="fw-semibold">{{ $patient->date_of_birth->age }}</div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="text-muted small">Gender</div>
                                <div class="fw-semibold">{{ ucfirst($patient->gender) }}</div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="text-muted small">Blood Group</div>
                                <div class="fw-semibold">{{ $patient->blood_group }}</div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="text-muted small">Phone</div>
                                <div class="fw-semibold">{{ Auth::user()->phone ?? '—' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Your patient profile is incomplete. Please contact reception.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-search-heart text-primary" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2">Find a Doctor</h5>
                    <p class="text-muted small mb-3">
                        Browse our specialists across {{ \App\Models\Department::count() }} departments.
                    </p>
                    <a href="{{ route('patient.doctors.index') }}" class="btn btn-primary">
                        Browse Doctors <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-calendar-week me-2"></i>Upcoming Appointments</strong>
                    @if($upcomingAppointments->isNotEmpty())
                        <a href="{{ route('patient.appointments.index') }}" class="small">View all</a>
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
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">Dr. {{ $appt->doctor->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $appt->appointment_date->format('D, M j') }} at
                                        {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                                        &middot; {{ $appt->doctor->department->name }}
                                    </div>
                                </div>
                                <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-file-medical me-2"></i>Recent Medical Records</strong>
                </div>
                @if($recentRecords->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-file-earmark-medical"></i>
                        <p class="text-muted mb-1">No records yet.</p>
                        <p class="text-muted small mb-0">Records appear after consultations (Phase 9).</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
