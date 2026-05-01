<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hospital Appointment System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <div class="auth-shell">
        {{-- Branded aside (hidden on mobile) --}}
        <aside class="auth-aside">
            <div class="auth-aside-content">
                <a href="/" class="auth-aside-brand">
                    <i class="bi bi-hospital"></i>
                    <span>{{ config('app.name') }}</span>
                </a>

                <h2 class="auth-aside-headline">
                    Healthcare,<br>without the queue.
                </h2>
                <p class="auth-aside-sub">
                    Book a 30-minute consultation with one of our {{ \App\Models\Doctor::count() }} specialists.
                    Real-time availability, instant confirmation, your records always accessible.
                </p>

                <ul class="auth-aside-features">
                    <li><i class="bi bi-check-lg"></i> Specialists across {{ \App\Models\Department::count() }} departments</li>
                    <li><i class="bi bi-check-lg"></i> Real-time slot availability</li>
                    <li><i class="bi bi-check-lg"></i> Diagnoses &amp; prescriptions in your account</li>
                    <li><i class="bi bi-check-lg"></i> Cancel anytime before your visit</li>
                </ul>
            </div>
        </aside>

        {{-- Form side --}}
        <main class="auth-main">
            <a href="/" class="auth-back">
                <i class="bi bi-arrow-left"></i> Back to home
            </a>

            <div class="auth-form-wrap">
                <a href="/" class="auth-mobile-brand">
                    <i class="bi bi-hospital"></i>
                    {{ config('app.name') }}
                </a>

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
