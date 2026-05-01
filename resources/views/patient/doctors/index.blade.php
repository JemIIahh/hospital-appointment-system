<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Browse Doctors</h2>
            <p class="text-muted small mb-0">Find a specialist and view their availability</p>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('patient.doctors.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="department_id" class="form-select">
                            <option value="">All departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by name or specialization"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->hasAny(['department_id','search']))
                            <a href="{{ route('patient.doctors.index') }}" class="btn btn-outline-secondary"
                               title="Clear filters">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Doctor list --}}
    @if($doctors->isEmpty())
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <p class="text-muted mb-2">No doctors match your filters.</p>
                <a href="{{ route('patient.doctors.index') }}" class="btn btn-sm btn-outline-primary">
                    Clear filters
                </a>
            </div>
        </div>
    @else
        @php
            $palettes = [
                'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
                'linear-gradient(135deg, #0891b2 0%, #06b6d4 100%)',
                'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
                'linear-gradient(135deg, #db2777 0%, #ec4899 100%)',
                'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)',
            ];
        @endphp
        <div class="row g-3">
            @foreach($doctors as $doctor)
                @php
                    $initials = collect(explode(' ', $doctor->user->name))
                        ->take(2)
                        ->map(fn ($p) => mb_substr($p, 0, 1))
                        ->implode('');
                    $bg = $palettes[$doctor->id % count($palettes)];
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 doctor-card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="doctor-avatar" style="background: {{ $bg }};">{{ strtoupper($initials) }}</span>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold text-truncate">Dr. {{ $doctor->user->name }}</h6>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-building me-1 text-primary"></i>{{ $doctor->department->name }}
                                    </span>
                                </div>
                            </div>
                            <p class="mb-2 small text-muted">
                                <i class="bi bi-stars me-1"></i>{{ $doctor->specialization }}
                            </p>
                            <p class="fw-semibold mb-3">
                                <span class="text-primary fs-5">${{ number_format($doctor->consultation_fee, 2) }}</span>
                                <span class="text-muted small fw-normal">/ consultation</span>
                            </p>
                            <a href="{{ route('patient.doctors.show', $doctor) }}"
                               class="btn btn-outline-primary mt-auto">
                                View profile <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $doctors->links() }}
        </div>
    @endif
</x-app-layout>
