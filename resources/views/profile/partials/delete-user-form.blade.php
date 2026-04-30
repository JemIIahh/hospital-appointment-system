<section>
    <header class="mb-3">
        <h2 class="h5 mb-1 text-danger">Delete Account</h2>
        <p class="text-muted small mb-0">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            Enter your password to confirm.
        </p>
    </header>

    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This cannot be undone.');">
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="delete_password" class="form-label">Password</label>
            <input id="delete_password" name="password" type="password"
                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                placeholder="Password" autocomplete="current-password">
            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-danger">Delete Account</button>
    </form>
</section>
