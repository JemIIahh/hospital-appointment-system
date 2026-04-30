<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="bi bi-building me-2"></i>Departments</h2>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Add Department
            </a>
        </div>
    </x-slot>

    @if($departments->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-building fs-1 text-muted"></i>
                <p class="text-muted mb-3">No departments yet.</p>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    Add the first department
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-center">Doctors</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                            <tr>
                                <td><strong>{{ $department->name }}</strong></td>
                                <td class="text-muted small">{{ Str::limit($department->description, 80) }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-secondary">{{ $department->doctors_count }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.departments.destroy', $department) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ $department->name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $departments->links() }}
        </div>
    @endif
</x-app-layout>
