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
    <nav class="app-nav navbar navbar-expand-lg">
        <div class="container app-nav-inner">
            <a class="app-nav-brand" href="{{ route('dashboard') }}">
                <span class="app-nav-brand-icon"><i class="bi bi-hospital"></i></span>
                <span class="app-nav-brand-name">{{ config('app.name') }}</span>
            </a>

            <button class="app-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>

            <div class="collapse navbar-collapse app-nav-collapse" id="mainNav">
                <ul class="app-nav-links">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li>
                                <a class="app-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-house-door"></i><span>Home</span>
                                </a>
                            </li>
                            @if(Route::has('admin.departments.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('admin.departments.*') ? 'is-active' : '' }}" href="{{ route('admin.departments.index') }}">
                                        <i class="bi bi-building"></i><span>Departments</span>
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('admin.doctors.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('admin.doctors.*') ? 'is-active' : '' }}" href="{{ route('admin.doctors.index') }}">
                                        <i class="bi bi-clipboard2-pulse"></i><span>Doctors</span>
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('admin.reports.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}" href="{{ route('admin.reports.index') }}">
                                        <i class="bi bi-bar-chart"></i><span>Reports</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Auth::user()->isDoctor())
                            <li>
                                <a class="app-nav-link {{ request()->routeIs('doctor.dashboard') ? 'is-active' : '' }}" href="{{ route('doctor.dashboard') }}">
                                    <i class="bi bi-house-door"></i><span>Home</span>
                                </a>
                            </li>
                            @if(Route::has('doctor.appointments.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('doctor.appointments.*') ? 'is-active' : '' }}" href="{{ route('doctor.appointments.index') }}">
                                        <i class="bi bi-calendar-check"></i><span>Appointments</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Auth::user()->isPatient())
                            <li>
                                <a class="app-nav-link {{ request()->routeIs('patient.dashboard') ? 'is-active' : '' }}" href="{{ route('patient.dashboard') }}">
                                    <i class="bi bi-house-door"></i><span>Home</span>
                                </a>
                            </li>
                            @if(Route::has('patient.doctors.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('patient.doctors.*') ? 'is-active' : '' }}" href="{{ route('patient.doctors.index') }}">
                                        <i class="bi bi-search-heart"></i><span>Browse Doctors</span>
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('patient.appointments.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('patient.appointments.*') ? 'is-active' : '' }}" href="{{ route('patient.appointments.index') }}">
                                        <i class="bi bi-calendar-check"></i><span>My Appointments</span>
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('patient.records.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('patient.records.*') ? 'is-active' : '' }}" href="{{ route('patient.records.index') }}">
                                        <i class="bi bi-file-medical"></i><span>Record</span>
                                    </a>
                                </li>
                            @endif
                            @if(Route::has('patient.prescriptions.index'))
                                <li>
                                    <a class="app-nav-link {{ request()->routeIs('patient.prescriptions.*') ? 'is-active' : '' }}" href="{{ route('patient.prescriptions.index') }}">
                                        <i class="bi bi-capsule"></i><span>Prescriptions</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endauth
                </ul>

                <ul class="app-nav-user">
                    @auth
                        <li class="dropdown">
                            <button class="app-nav-userbtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="app-nav-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="app-nav-userinfo">
                                    <span class="app-nav-username">{{ Auth::user()->name }}</span>
                                    <span class="app-nav-userrole">{{ ucfirst(Auth::user()->role) }}</span>
                                </span>
                                <i class="bi bi-chevron-down app-nav-chev"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="dropdown-header small text-muted">
                                    Signed in as <strong>{{ Auth::user()->email }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
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
