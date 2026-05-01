<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <a href="{{ route('patient.doctors.index') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left"></i> Back to all doctors
                </a>
                <h2 class="h4 mb-0 mt-1">Doctor Profile</h2>
            </div>
        </div>
    </x-slot>

    @php
        $palettes = [
            'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
            'linear-gradient(135deg, #0891b2 0%, #06b6d4 100%)',
            'linear-gradient(135deg, #059669 0%, #10b981 100%)',
            'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
            'linear-gradient(135deg, #db2777 0%, #ec4899 100%)',
            'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)',
        ];
        $initials = collect(explode(' ', $doctor->user->name))
            ->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
        $bg = $palettes[$doctor->id % count($palettes)];
    @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            {{-- Hero card --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center mb-3 flex-wrap">
                        <span class="doctor-avatar doctor-avatar-xl" style="background: {{ $bg }};">{{ strtoupper($initials) }}</span>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="mb-1">Dr. {{ $doctor->user->name }}</h3>
                            <p class="text-muted mb-2"><i class="bi bi-stars me-1"></i>{{ $doctor->specialization }}</p>
                            <span class="badge text-bg-light border">
                                <i class="bi bi-building me-1 text-primary"></i>{{ $doctor->department->name }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-sm-6">
                            <div class="profile-stat">
                                <i class="bi bi-cash-coin"></i>
                                <div>
                                    <div class="text-muted small">Consultation fee</div>
                                    <div class="fw-semibold text-primary">${{ number_format($doctor->consultation_fee, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="profile-stat">
                                <i class="bi bi-card-checklist"></i>
                                <div>
                                    <div class="text-muted small">License</div>
                                    <div class="fw-semibold">{{ $doctor->license_number }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About --}}
            @if($doctor->bio)
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <strong><i class="bi bi-info-circle me-2 text-primary"></i>About Dr. {{ explode(' ', $doctor->user->name)[0] }}</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="line-height: 1.7;">{{ $doctor->bio }}</p>
                    </div>
                </div>
            @endif

            {{-- Contact --}}
            <div class="card">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-envelope me-2 text-primary"></i>Contact</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-at text-muted"></i>
                        <span>{{ $doctor->user->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-calendar-week me-2 text-primary"></i>Weekly Schedule</strong>
                </div>
                @if($doctor->schedules->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p class="text-muted mb-0 small">No schedule set.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($doctor->schedules as $sched)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">{{ ucfirst($sched->day_of_week) }}</span>
                                <span class="text-muted small">
                                    {{ \Carbon\Carbon::parse($sched->start_time)->format('g:i A') }}
                                    – {{ \Carbon\Carbon::parse($sched->end_time)->format('g:i A') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="card-body">
                    <a href="{{ route('patient.appointments.create', $doctor) }}" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
