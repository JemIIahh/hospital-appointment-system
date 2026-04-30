<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Doctor Dashboard</h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <p class="mb-1">Welcome, <strong>Dr. {{ Auth::user()->name }}</strong>.</p>
            <p class="text-muted small mb-0">
                Phase 2 placeholder — consultation tools arrive in Phase 9.
            </p>
        </div>
    </div>
</x-app-layout>
