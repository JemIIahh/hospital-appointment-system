<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: .06em;">Administrator</p>
                <h2 class="h3 mb-0">Welcome, {{ Str::before(Auth::user()->name, ' ') }} 👋</h2>
                <p class="text-muted mb-0">Hospital overview and management.</p>
            </div>
            @if(Route::has('admin.reports.index'))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
                    <i class="bi bi-bar-chart me-1"></i> View reports
                </a>
            @endif
        </div>
    </x-slot>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.departments.index') }}" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
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
                <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
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
            <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
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
            <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
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

    {{-- Quick actions + helpful nav --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-lightning-charge me-2 text-primary"></i>Quick actions</strong>
                </div>
                <div class="card-body">
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
                        @if(Route::has('admin.reports.index'))
                            <a href="{{ route('admin.reports.index') }}" class="action-tile">
                                <i class="bi bi-bar-chart-line"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Open reports dashboard</div>
                                    <div class="small text-muted">Booking trends, revenue, doctor utilization</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100 hospital-info-card">
                <div class="card-body">
                    <h5 class="card-title mb-2"><i class="bi bi-hospital me-2 text-primary"></i>{{ config('app.name') }}</h5>
                    <p class="text-muted small mb-3">
                        Use the navigation to manage departments and doctors, or open reports for hospital-wide analytics.
                    </p>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="small mb-0 text-muted">System status</p>
                            <p class="mb-0 fw-semibold"><span class="status-dot status-dot-ok"></span>All services operational</p>
                        </div>
                        <span class="badge text-bg-light border">v1.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
