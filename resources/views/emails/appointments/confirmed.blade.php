<x-mail::message>
# Appointment Confirmed

Hello {{ $appointment->patient->user->name }},

Good news — Dr. {{ $appointment->doctor->user->name }} has confirmed your appointment.

**Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
**Department:** {{ $appointment->doctor->department->name }}

Please arrive 10 minutes early. Bring any relevant medical history or current medications you are taking.

<x-mail::button :url="route('patient.appointments.show', $appointment)">
View Appointment
</x-mail::button>

Thanks,
{{ config('app.name') }}
</x-mail::message>
