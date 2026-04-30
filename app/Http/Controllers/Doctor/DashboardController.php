<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return view('doctor.dashboard', [
                'doctor'            => null,
                'todayAppointments' => collect(),
                'stats'             => ['today' => 0, 'pending' => 0, 'thisWeek' => 0, 'totalCompleted' => 0],
            ]);
        }

        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->where('status', '!=', 'cancelled')
            ->with('patient.user')
            ->orderBy('appointment_time')
            ->get();

        $stats = [
            'today'    => $todayAppointments->count(),
            'pending'  => Appointment::where('doctor_id', $doctor->id)
                ->where('status', 'pending')
                ->whereDate('appointment_date', '>=', today())
                ->count(),
            'thisWeek' => Appointment::where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [today(), today()->endOfWeek()])
                ->where('status', '!=', 'cancelled')
                ->count(),
            'totalCompleted' => Appointment::where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('doctor.dashboard', compact('doctor', 'todayAppointments', 'stats'));
    }
}
