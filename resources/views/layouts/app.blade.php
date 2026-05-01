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
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand navbar-brand-app" href="{{ route('dashboard') }}">
                <i class="bi bi-hospital me-2"></i>{{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            @if(Route::has('admin.departments.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">Departments</a>
                                </li>
                            @endif
                            @if(Route::has('admin.doctors.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}">Doctors</a>
                                </li>
                            @endif
                            @if(Route::has('admin.reports.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">Reports</a>
                                </li>
                            @endif
                        @elseif(Auth::user()->isDoctor())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">Dashboard</a>
                            </li>
                            @if(Route::has('doctor.appointments.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}" href="{{ route('doctor.appointments.index') }}">Appointments</a>
                                </li>
                            @endif
                        @elseif(Auth::user()->isPatient())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">Dashboard</a>
                            </li>
                            @if(Route::has('patient.doctors.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('patient.doctors.*') ? 'active' : '' }}" href="{{ route('patient.doctors.index') }}">Browse Doctors</a>
                                </li>
                            @endif
                            @if(Route::has('patient.appointments.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}" href="{{ route('patient.appointments.index') }}">My Appointments</a>
                                </li>
                            @endif
                            @if(Route::has('patient.records.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('patient.records.*') ? 'active' : '' }}" href="{{ route('patient.records.index') }}">My Records</a>
                                </li>
                            @endif
                            @if(Route::has('patient.prescriptions.index'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('patient.prescriptions.*') ? 'active' : '' }}" href="{{ route('patient.prescriptions.index') }}">Prescriptions</a>
                                </li>
                            @endif
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="dropdown-header text-muted small">
                                    Logged in as <strong>{{ Auth::user()->role }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="page-header">
            <div class="container py-3">{{ $header }}</div>
        </header>
    @endisset

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main class="container py-4">
        {{ $slot }}
    </main>

    <footer class="py-4 border-top bg-white mt-5">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; AUST Abuja final-year project
        </div>
    </footer>
</body>
</html>
