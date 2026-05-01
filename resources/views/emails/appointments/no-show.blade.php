<x-mail::message>
# Missed Appointment

Hello {{ $appointment->patient->user->name }},

Our records show that you did not attend your appointment with Dr. {{ $appointment->doctor->user->name }}.

**Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
**Department:** {{ $appointment->doctor->department->name }}

If you would like to reschedule, you can book a new appointment any time.

<x-mail::button :url="route('patient.doctors.show', $appointment->doctor)">
Book Again
</x-mail::button>

Thanks,
{{ config('app.name') }}
</x-mail::message>
