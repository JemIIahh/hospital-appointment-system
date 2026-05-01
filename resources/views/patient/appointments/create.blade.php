<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('patient.doctors.show', $doctor) }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left"></i> Back to doctor profile
            </a>
            <h2 class="h4 mb-0 mt-1">Book Appointment</h2>
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

    {{-- Doctor strip --}}
    <div class="card mb-4">
        <div class="card-body d-flex gap-3 align-items-center flex-wrap">
            <span class="doctor-avatar" style="background: {{ $bg }};">{{ strtoupper($initials) }}</span>
            <div class="flex-grow-1 min-w-0">
                <h5 class="mb-0 fw-semibold">Dr. {{ $doctor->user->name }}</h5>
                <p class="text-muted small mb-0">
                    <span class="me-2"><i class="bi bi-building me-1"></i>{{ $doctor->department->name }}</span>
                    <span><i class="bi bi-stars me-1"></i>{{ $doctor->specialization }}</span>
                </p>
            </div>
            <div class="text-end">
                <div class="text-muted small">Consultation fee</div>
                <div class="fw-semibold text-primary fs-5">${{ number_format($doctor->consultation_fee, 2) }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('patient.appointments.store') }}" x-data="{ time: '' }">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

        {{-- Step 1: pick a date --}}
        <div class="card mb-3 step-card">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <span class="step-num">1</span>
                <div>
                    <strong>Pick a date</strong>
                    <span class="text-muted small d-block">Next 30 days, on which Dr. {{ explode(' ', $doctor->user->name)[0] }} works</span>
                </div>
            </div>
            <div class="card-body">
                @if($bookableDates->isEmpty())
                    <p class="text-muted mb-0">This doctor has no availability in the next 30 days.</p>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($bookableDates as $d)
                            @php $isSel = $selectedDate && $selectedDate->isSameDay($d); @endphp
                            <a href="{{ route('patient.appointments.create', ['doctor' => $doctor, 'date' => $d->toDateString()]) }}"
                               class="date-pill {{ $isSel ? 'is-selected' : '' }}">
                                <span class="date-pill-dow">{{ $d->format('D') }}</span>
                                <span class="date-pill-day">{{ $d->format('j') }}</span>
                                <span class="date-pill-month">{{ $d->format('M') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($selectedDate)
            {{-- Step 2: pick a time slot --}}
            <input type="hidden" name="appointment_date" value="{{ $selectedDate->toDateString() }}">
            <input type="hidden" name="appointment_time" :value="time">

            <div class="card mb-3 step-card">
                <div class="card-header bg-white d-flex align-items-center gap-2">
                    <span class="step-num">2</span>
                    <div>
                        <strong>Pick a time</strong>
                        <span class="text-muted small d-block">{{ $selectedDate->format('l, F j') }} &middot; 30 min slots</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($slots->isEmpty())
                        <p class="text-muted mb-0">No slots configured for this day.</p>
                    @else
                        @php
                            $availableCount = $slots->where('is_available', true)->count();
                        @endphp

                        @if($availableCount === 0)
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                All slots on this day are booked or in the past. Please pick another date.
                            </div>
                        @endif

                        <div class="slot-grid">
                            @foreach($slots as $slot)
                                @if($slot['is_available'])
                                    <button type="button"
                                        @click="time = '{{ $slot['time'] }}'"
                                        :class="time === '{{ $slot['time'] }}' ? 'is-selected' : ''"
                                        class="slot-pill">
                                        {{ $slot['display'] }}
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="slot-pill is-disabled"
                                        title="{{ $slot['is_booked'] ? 'Already booked' : 'In the past' }}">
                                        {{ $slot['display'] }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Step 3: reason --}}
            <div class="card mb-3 step-card" x-show="time" style="display:none">
                <div class="card-header bg-white d-flex align-items-center gap-2">
                    <span class="step-num">3</span>
                    <div>
                        <strong>Reason for visit</strong>
                        <span class="text-muted small d-block">Helps the doctor prepare</span>
                    </div>
                </div>
                <div class="card-body">
                    <textarea name="reason" rows="3" required maxlength="500"
                        class="form-control @error('reason') is-invalid @enderror"
                        placeholder="Briefly describe your symptoms or the reason for booking.">{{ old('reason') }}</textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4" x-show="time" style="display:none">
                <a href="{{ route('patient.doctors.show', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-calendar-check me-1"></i> Confirm Booking
                </button>
            </div>
        @endif
    </form>
</x-app-layout>
