<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0"><i class="bi bi-clipboard2-plus me-2"></i>Add Doctor</h2>
    </x-slot>

    <div class="row">
        <div class="col-12 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small">
                        Creates a new user account (role <code>doctor</code>) with a system-generated
                        temporary password. The password will appear once on the next page —
                        share it securely with the doctor.
                    </p>
                    <hr>
                    <form action="{{ route('admin.doctors.store') }}" method="POST">
                        @include('admin.doctors._form')

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Doctor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
