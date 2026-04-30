<x-guest-layout>
    <h2 class="h5 mb-3">Verify Email</h2>
    <p class="text-muted small mb-3">
        Thanks for signing up! Please verify your email address by clicking the link we just sent you.
        If you didn't receive it, we can resend it.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none small">Log Out</button>
        </form>
    </div>
</x-guest-layout>
