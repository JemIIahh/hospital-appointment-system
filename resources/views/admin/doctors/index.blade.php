<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Doctors</h2>
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Add Doctor
            </a>
        </div>
    </x-slot>

    @if($doctors->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard2-pulse fs-1 text-muted"></i>
                <p class="text-muted mb-3">No doctors yet.</p>
                <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                    Add the first doctor
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
                            <th>Email</th>
                            <th>Department</th>
                            <th>Specialization</th>
                            <th class="text-end">Fee</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                            <tr>
                                <td><strong>{{ $doctor->user->name }}</strong></td>
                                <td class="small">{{ $doctor->user->email }}</td>
                                <td><span class="badge text-bg-secondary">{{ $doctor->department->name }}</span></td>
                                <td>{{ $doctor->specialization }}</td>
                                <td class="text-end">${{ number_format($doctor->consultation_fee, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.doctors.destroy', $doctor) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete Dr. {{ $doctor->user->name }}? This will remove their account.');">
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
            {{ $doctors->links() }}
        </div>
    @endif
</x-app-layout>
