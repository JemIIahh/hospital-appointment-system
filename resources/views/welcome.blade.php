<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hospital Appointment System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page">

{{-- Sticky nav --}}
<nav class="welcome-nav"
     x-data="{ scrolled: false }"
     x-init="scrolled = window.scrollY > 30"
     @scroll.window="scrolled = window.scrollY > 30"
     :class="{ 'is-scrolled': scrolled }">
    <div class="container">
        <a href="/" class="welcome-nav-brand">
            <i class="bi bi-hospital"></i>
            <span>{{ config('app.name') }}</span>
        </a>
        <div class="welcome-nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="welcome-nav-link d-none d-sm-inline">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get started</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero --}}
<section class="welcome-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-badge">
                    <i class="bi bi-shield-check"></i>
                    Trusted hospital care
                </span>
                <h1 class="hero-title">
                    Healthcare<br>made <span class="hero-title-accent">simple.</span>
                </h1>
                <p class="hero-lead">
                    Book appointments with our specialists, manage your medical records,
                    and access prescriptions &mdash; all in one place. No phone calls. No waiting rooms.
                </p>
                <div class="hero-cta">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Get started
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-link">
                            Already have an account?
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            Continue to dashboard
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endguest
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>{{ \App\Models\Department::count() }}</strong>
                        <span>Specialties</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <strong>{{ \App\Models\Doctor::count() }}+</strong>
                        <span>Specialist doctors</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <strong>30 min</strong>
                        <span>Per consultation</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <img
                        src="https://images.unsplash.com/photo-1666214280557-f1b5022eb634?auto=format&fit=crop&w=800&q=80"
                        alt="Healthcare professional consulting with patient"
                        class="hero-image"
                        loading="eager"
                    >
                    <div class="floating-card floating-card-top">
                        <i class="bi bi-calendar-check text-success"></i>
                        <div>
                            <strong>Appointment confirmed</strong>
                            <span>Friday at 2:30 PM</span>
                        </div>
                    </div>
                    <div class="floating-card floating-card-bottom">
                        <i class="bi bi-people-fill text-primary"></i>
                        <div class="floating-stat">
                            <strong>{{ \App\Models\Doctor::count() }} doctors</strong>
                            <span>across {{ \App\Models\Department::count() }} departments</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="welcome-section bg-white">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <span class="section-eyebrow">How it works</span>
            <h2 class="section-title display-6">Three steps to better care</h2>
            <p class="section-lead">
                From browsing specialists to seeing them in person &mdash; everything in one flow.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 reveal">
                <div class="how-step">
                    <div class="how-step-number">01</div>
                    <i class="bi bi-search how-step-icon"></i>
                    <h3>Find a specialist</h3>
                    <p>
                        Browse {{ \App\Models\Doctor::count() }} doctors across
                        {{ \App\Models\Department::count() }} departments. Filter by specialty,
                        search by name, see consultation fees upfront.
                    </p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 100ms">
                <div class="how-step">
                    <div class="how-step-number">02</div>
                    <i class="bi bi-calendar-check how-step-icon"></i>
                    <h3>Pick a time slot</h3>
                    <p>
                        See real-time availability. Choose any 30-minute window that fits your
                        schedule. Booking is instant &mdash; no confirmation calls needed.
                    </p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 200ms">
                <div class="how-step">
                    <div class="how-step-number">03</div>
                    <i class="bi bi-file-medical how-step-icon"></i>
                    <h3>Show up &amp; review</h3>
                    <p>
                        Attend your appointment. Diagnoses, notes, and prescriptions are saved to
                        your account &mdash; accessible whenever you need them.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Specialties --}}
<section class="welcome-section">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <span class="section-eyebrow">Specialties</span>
            <h2 class="section-title display-6">
                Care across {{ \App\Models\Department::count() }} departments
            </h2>
            <p class="section-lead">
                Expert specialists in the areas of medicine you're most likely to need.
            </p>
        </div>

        <div class="row g-3">
            @php
                $deptIcons = [
                    'Cardiology'        => 'bi-heart-pulse',
                    'Pediatrics'        => 'bi-emoji-smile',
                    'Orthopedics'       => 'bi-bandaid',
                    'Dermatology'       => 'bi-droplet',
                    'Neurology'         => 'bi-cpu',
                    'General Medicine'  => 'bi-prescription2',
                    'Emergency'         => 'bi-exclamation-octagon',
                ];
            @endphp
            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                <div class="col-md-6 col-lg-3 reveal" style="transition-delay: {{ ($loop->index % 4) * 80 }}ms">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="bi {{ $deptIcons[$dept->name] ?? 'bi-clipboard2-pulse' }}"></i>
                        </div>
                        <h4>{{ $dept->name }}</h4>
                        <p>{{ Str::limit($dept->description, 75) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Final CTA --}}
@guest
    <section class="welcome-cta">
        <div class="container">
            <div class="cta-card reveal">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2>Ready to book your first appointment?</h2>
                        <p>Registration takes under a minute. No fees, no commitments.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                            Register as a patient
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endguest

{{-- Footer --}}
<footer class="welcome-footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="welcome-footer-brand">
                    <i class="bi bi-hospital"></i>
                    {{ config('app.name') }}
                </div>
                <p class="welcome-footer-tagline">
                    A final-year project for African University of Science and Technology, Abuja.
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }} &middot; All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
