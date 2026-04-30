<section>
    <header class="mb-3">
        <h2 class="h5 mb-1">Profile Information</h2>
        <p class="text-muted small mb-0">Update your account's name and email address.</p>
    </header>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                class="form-control @error('name') is-invalid @enderror"
                required autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                class="form-control @error('email') is-invalid @enderror"
                required autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Save</button>
            @if (session('status') === 'profile-updated')
                <span class="text-muted small">Saved.</span>
            @endif
        </div>
    </form>
</section>
