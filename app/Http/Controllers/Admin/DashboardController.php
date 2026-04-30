<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'departmentCount'  => Department::count(),
            'doctorCount'      => Doctor::count(),
            'patientCount'     => Patient::count(),
            'appointmentCount' => Appointment::count(),
        ]);
    }
}
