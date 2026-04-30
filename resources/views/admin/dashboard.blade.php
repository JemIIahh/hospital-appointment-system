<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">Admin Dashboard</h2>
                <p class="text-muted small mb-0">Hospital overview and management</p>
            </div>
            <span class="badge text-bg-primary">{{ Auth::user()->name }}</span>
        </div>
    </x-slot>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.departments.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Departments</div>
                            <div class="stat-number">{{ $departmentCount }}</div>
                        </div>
                        <i class="bi bi-building stat-icon"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.doctors.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Doctors</div>
                            <div class="stat-number">{{ $doctorCount }}</div>
                        </div>
                        <i class="bi bi-clipboard2-pulse stat-icon"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Patients</div>
                        <div class="stat-number">{{ $patientCount }}</div>
                    </div>
                    <i class="bi bi-person-heart stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Appointments</div>
                        <div class="stat-number">{{ $appointmentCount }}</div>
                    </div>
                    <i class="bi bi-calendar-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions + welcome --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.departments.create') }}" class="action-tile">
                            <i class="bi bi-building-add"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Add a department</div>
                                <div class="small text-muted">e.g. Pulmonology, Oncology</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.doctors.create') }}" class="action-tile">
                            <i class="bi bi-clipboard2-plus"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Onboard a doctor</div>
                                <div class="small text-muted">Create a new doctor account with credentials</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-2">Welcome, {{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-3">
                        Use the navigation to manage departments and doctors.
                        Reports and appointment monitoring arrive in Phase 12.
                    </p>
                    <hr>
                    <p class="small mb-1"><strong>Build phase:</strong> Phase 6 of 14</p>
                    <p class="small mb-0 text-muted">
                        Booking engine, prescriptions, and reports are next.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
