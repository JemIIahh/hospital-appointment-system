<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hospital Appointment System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex flex-column min-vh-100 align-items-center justify-content-center py-4 px-3">
        <a href="/" class="auth-brand mb-4">
            <i class="bi bi-hospital"></i>{{ config('app.name') }}
        </a>

        <div class="card auth-card" style="width: 100%; max-width: 26rem;">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>

        <p class="text-muted small mt-3 mb-0">
            <a href="/" class="text-decoration-none text-muted">&larr; Back to home</a>
        </p>
    </div>
</body>
</html>
