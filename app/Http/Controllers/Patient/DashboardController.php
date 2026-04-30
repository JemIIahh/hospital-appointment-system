<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $patient = Auth::user()->patient;

        return view('patient.dashboard', [
            'patient'              => $patient,
            'upcomingAppointments' => collect(),
            'recentRecords'        => collect(),
        ]);
    }
}
