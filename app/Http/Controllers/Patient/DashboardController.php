<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $patient = Auth::user()->patient;

        $upcomingAppointments = $patient
            ? Appointment::where('patient_id', $patient->id)
                ->upcoming()
                ->with(['doctor.user', 'doctor.department'])
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->take(5)
                ->get()
            : collect();

        $recentRecords = $patient
            ? MedicalRecord::whereHas('appointment', fn ($q) => $q->where('patient_id', $patient->id))
                ->with(['appointment.doctor.user', 'appointment.doctor.department'])
                ->latest('id')
                ->take(3)
                ->get()
            : collect();

        return view('patient.dashboard', [
            'patient'              => $patient,
            'upcomingAppointments' => $upcomingAppointments,
            'recentRecords'        => $recentRecords,
        ]);
    }
}
