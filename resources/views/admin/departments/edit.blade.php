<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0"><i class="bi bi-building-gear me-2"></i>Edit Department</h2>
    </x-slot>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @method('PATCH')
                        @include('admin.departments._form')

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
