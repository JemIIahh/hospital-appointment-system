<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hospital Appointment System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand navbar-brand-app">
                <i class="bi bi-hospital me-2"></i>{{ config('app.name') }}
            </span>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero-wrap">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-light text-primary mb-3 px-3 py-2 fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> Trusted hospital care
                    </span>
                    <h1 class="display-4 mb-3">Book a doctor.<br>Get the care you need.</h1>
                    <p class="lead mb-4" style="max-width: 36rem;">
                        Find specialists across {{ \App\Models\Department::count() }} departments,
                        book a 30-minute slot at a time that suits you, and access your medical
                        records and prescriptions whenever you need them.
                    </p>
                    @guest
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-person-plus me-1"></i> Register as a Patient
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                I already have an account
                            </a>
                        </div>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                            Continue to Dashboard <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endguest
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                    <i class="bi bi-hospital" style="font-size: 12rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">How it works</div>
                <h2 class="section-title">Three steps. No phone calls.</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="step-number">1</div>
                    <h5 class="fw-semibold">Find a doctor</h5>
                    <p class="text-muted mb-0">
                        Browse our specialists by department or specialization to find the right
                        doctor for your needs.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="step-number">2</div>
                    <h5 class="fw-semibold">Pick a time slot</h5>
                    <p class="text-muted mb-0">
                        See real-time availability and book a 30-minute appointment that fits
                        your schedule.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="step-number">3</div>
                    <h5 class="fw-semibold">Show up &amp; review</h5>
                    <p class="text-muted mb-0">
                        Attend your appointment. Your records and prescriptions are saved to
                        your account afterwards.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">Why this system</div>
                <h2 class="section-title">Everything in one place</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <i class="bi bi-calendar-check"></i>
                        <h6>Easy Booking</h6>
                        <p>30-minute slots, real-time availability, instant confirmation.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <h6>Specialist Doctors</h6>
                        <p>{{ \App\Models\Doctor::count() }} doctors across {{ \App\Models\Department::count() }} departments.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <i class="bi bi-file-medical"></i>
                        <h6>Medical Records</h6>
                        <p>Diagnosis, notes, and consultation history kept in your account.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <i class="bi bi-shield-check"></i>
                        <h6>Secure &amp; Private</h6>
                        <p>Encrypted, role-based access. Only you and your doctor see your records.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    @guest
        <section class="py-5">
            <div class="container">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <h3 class="fw-semibold mb-3">Ready to book your first appointment?</h3>
                        <p class="text-muted mb-4">Registration takes under a minute.</p>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Register as a Patient <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endguest

    <footer class="py-4 border-top bg-white">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Final-year project, AUST Abuja
        </div>
    </footer>
</body>
</html>
