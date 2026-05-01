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
    <div class="auth-bg">
        <div class="auth-bg-blob auth-bg-blob-1"></div>
        <div class="auth-bg-blob auth-bg-blob-2"></div>
        <div class="auth-bg-grid"></div>
    </div>

    <div class="auth-stack">
        <a href="/" class="auth-brand-large">
            <span class="auth-brand-icon"><i class="bi bi-hospital"></i></span>
            <span class="auth-brand-name">{{ config('app.name') }}</span>
        </a>

        <div class="auth-card-modern">
            {{ $slot }}
        </div>

        <a href="/" class="auth-back-link">
            <i class="bi bi-arrow-left"></i> Back to home
        </a>
    </div>
</body>
</html>
