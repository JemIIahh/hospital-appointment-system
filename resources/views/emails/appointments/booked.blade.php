<x-mail::message>
# Appointment Booked

Hello {{ $appointment->patient->user->name }},

Your appointment with **Dr. {{ $appointment->doctor->user->name }}** has been booked. The doctor will confirm shortly.

**Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
**Department:** {{ $appointment->doctor->department->name }}
**Specialization:** {{ $appointment->doctor->specialization }}
**Consultation fee:** ${{ number_format($appointment->doctor->consultation_fee, 2) }}

**Reason for visit:**
> {{ $appointment->reason }}

**Status:** Pending — awaiting doctor confirmation.

<x-mail::button :url="route('patient.appointments.show', $appointment)">
View Appointment
</x-mail::button>

If you need to cancel, you can do so from the appointment page until the appointment date.

Thanks,
{{ config('app.name') }}
</x-mail::message>
