<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Doctor::with(['user', 'department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('specialization', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $doctors = $query
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->select('doctors.*')
            ->orderBy('users.name')
            ->paginate(12)
            ->withQueryString();

        return view('patient.doctors.index', [
            'doctors'     => $doctors,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function show(Doctor $doctor): View
    {
        $doctor->load(['user', 'department', 'schedules']);

        $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $doctor->setRelation(
            'schedules',
            $doctor->schedules->sortBy(fn ($s) => array_search($s->day_of_week, $orderedDays))->values()
        );

        return view('patient.doctors.show', compact('doctor'));
    }
}
