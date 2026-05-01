<x-mail::message>
# Appointment Cancelled

@if($cancelledBy === 'patient')
Hello Dr. {{ $appointment->doctor->user->name }},

The patient {{ $appointment->patient->user->name }} has cancelled their appointment.
@else
Hello {{ $appointment->patient->user->name }},

Your appointment with Dr. {{ $appointment->doctor->user->name }} has been cancelled by the doctor's office.
@endif

**Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
**Department:** {{ $appointment->doctor->department->name }}

The time slot is now available for re-booking.

@if($cancelledBy === 'doctor')
<x-mail::button :url="route('patient.doctors.index')">
Browse Doctors
</x-mail::button>
@endif

Thanks,
{{ config('app.name') }}
</x-mail::message>
