<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Book Appointment</h2>
            <a href="{{ route('patient.doctors.show', $doctor) }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Back to doctor profile
            </a>
        </div>
    </x-slot>

    {{-- Doctor card --}}
    <div class="card mb-3">
        <div class="card-body d-flex gap-3 align-items-center">
            <i class="bi bi-person-circle text-muted" style="font-size: 3rem;"></i>
            <div class="flex-grow-1">
                <h5 class="mb-1">{{ $doctor->user->name }}</h5>
                <span class="badge text-bg-secondary me-1">{{ $doctor->department->name }}</span>
                <span class="text-muted small">{{ $doctor->specialization }}</span>
            </div>
            <div class="text-end">
                <div class="text-muted small">Consultation fee</div>
                <div class="fw-semibold text-primary">${{ number_format($doctor->consultation_fee, 2) }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('patient.appointments.store') }}" x-data="{ time: '' }">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

        {{-- Step 1: pick a date --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>1. Pick a date</strong>
                <span class="text-muted small">(next 30 days, on which Dr. {{ explode(' ', $doctor->user->name)[0] }} works)</span>
            </div>
            <div class="card-body">
                @if($bookableDates->isEmpty())
                    <p class="text-muted mb-0">This doctor has no availability in the next 30 days.</p>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($bookableDates as $d)
                            <a href="{{ route('patient.appointments.create', ['doctor' => $doctor, 'date' => $d->toDateString()]) }}"
                               class="btn btn-sm {{ $selectedDate && $selectedDate->isSameDay($d) ? 'btn-primary' : 'btn-outline-primary' }}">
                                <div class="lh-1">
                                    <div class="small">{{ $d->format('D') }}</div>
                                    <div class="fw-semibold">{{ $d->format('M j') }}</div>
                                </div>
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

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <strong>2. Pick a time on {{ $selectedDate->format('l, F j') }}</strong>
                    <span class="text-muted small">(30 min slots)</span>
                </div>
                <div class="card-body">
                    @if($slots->isEmpty())
                        <p class="text-muted mb-0">No slots configured for this day.</p>
                    @else
                        @php
                            $availableCount = $slots->where('is_available', true)->count();
                        @endphp

                        @if($availableCount === 0)
                            <p class="text-warning mb-3">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                All slots on this day are booked or in the past. Try another date.
                            </p>
                        @endif

                        <div class="d-flex flex-wrap gap-2">
                            @foreach($slots as $slot)
                                @if($slot['is_available'])
                                    <button type="button"
                                        @click="time = '{{ $slot['time'] }}'"
                                        :class="time === '{{ $slot['time'] }}' ? 'btn-primary' : 'btn-outline-primary'"
                                        class="btn btn-sm">
                                        {{ $slot['display'] }}
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="btn btn-sm btn-outline-secondary text-decoration-line-through"
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
            <div class="card mb-3" x-show="time" style="display:none">
                <div class="card-header bg-white">
                    <strong>3. Reason for visit</strong>
                </div>
                <div class="card-body">
                    <textarea name="reason" rows="3" required maxlength="500"
                        class="form-control @error('reason') is-invalid @enderror"
                        placeholder="Briefly describe your symptoms or the reason for booking. Helps the doctor prepare.">{{ old('reason') }}</textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4" x-show="time" style="display:none">
                <a href="{{ route('patient.doctors.show', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-calendar-check me-1"></i> Book Appointment
                </button>
            </div>
        @endif
    </form>
</x-app-layout>
