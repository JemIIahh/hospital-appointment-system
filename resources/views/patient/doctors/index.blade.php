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
        <div class="row g-3">
            @foreach($doctors as $doctor)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <i class="bi bi-person-circle text-muted" style="font-size: 2.5rem;"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">{{ $doctor->user->name }}</h6>
                                    <span class="badge text-bg-secondary mt-1">{{ $doctor->department->name }}</span>
                                </div>
                            </div>
                            <p class="mb-1 small">{{ $doctor->specialization }}</p>
                            <p class="fw-semibold mb-3 text-primary">
                                ${{ number_format($doctor->consultation_fee, 2) }} <span class="text-muted small fw-normal">/ consultation</span>
                            </p>
                            <a href="{{ route('patient.doctors.show', $doctor) }}"
                               class="btn btn-outline-primary mt-auto">
                                View profile
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
