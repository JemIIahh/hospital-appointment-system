<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">Doctor Profile</h2>
                <a href="{{ route('patient.doctors.index') }}" class="text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Back to all doctors
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <i class="bi bi-person-circle text-muted" style="font-size: 4rem;"></i>
                        <div class="flex-grow-1">
                            <h3 class="mb-1">{{ $doctor->user->name }}</h3>
                            <span class="badge text-bg-secondary mb-2">{{ $doctor->department->name }}</span>
                            <p class="mb-1">{{ $doctor->specialization }}</p>
                            <p class="text-primary fw-semibold mb-0">
                                ${{ number_format($doctor->consultation_fee, 2) }}
                                <span class="text-muted small fw-normal">/ consultation</span>
                            </p>
                        </div>
                    </div>

                    @if($doctor->bio)
                        <hr>
                        <h6 class="text-uppercase small text-muted mb-2">About</h6>
                        <p class="mb-0">{{ $doctor->bio }}</p>
                    @endif

                    <hr>
                    <div class="row text-muted small">
                        <div class="col-6">
                            <div class="text-uppercase">License</div>
                            <div class="text-dark">{{ $doctor->license_number }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-uppercase">Contact</div>
                            <div class="text-dark">{{ $doctor->user->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-calendar-week me-2"></i>Weekly Schedule</strong>
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
                    <button class="btn btn-primary w-100" disabled>
                        <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                    </button>
                    <p class="text-center text-muted small mt-2 mb-0">
                        Booking goes live in Phase 8
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
