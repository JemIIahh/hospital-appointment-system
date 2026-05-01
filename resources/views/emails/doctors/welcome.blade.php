<x-mail::message>
# Welcome, Dr. {{ $doctor->user->name }}

Your doctor account at {{ config('app.name') }} has been created by the hospital administrator.

**Email:** {{ $doctor->user->email }}
**Department:** {{ $doctor->department->name }}
**Specialization:** {{ $doctor->specialization }}
**License number:** {{ $doctor->license_number }}

Your **temporary password** is below. Please log in and change it immediately via the Profile page.

<x-mail::panel>
{{ $tempPassword }}
</x-mail::panel>

<x-mail::button :url="route('login')">
Log in
</x-mail::button>

If you did not expect this email, please contact the hospital administrator.

Thanks,
{{ config('app.name') }}
</x-mail::message>
