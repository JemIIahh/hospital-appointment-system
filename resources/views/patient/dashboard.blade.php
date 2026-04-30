<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0"><i class="bi bi-person-heart me-2"></i>Patient Dashboard</h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <p class="mb-1">Welcome, <strong>{{ Auth::user()->name }}</strong>.</p>
            <p class="text-muted small mb-0">
                Phase 2 placeholder — booking and appointments arrive in Phase 7 and 8.
            </p>
        </div>
    </div>
</x-app-layout>
