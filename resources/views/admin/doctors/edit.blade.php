<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0"><i class="bi bi-clipboard2-check me-2"></i>Edit Doctor</h2>
    </x-slot>

    <div class="row">
        <div class="col-12 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
                        @method('PATCH')
                        @include('admin.doctors._form')

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
