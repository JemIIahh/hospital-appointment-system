<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Allowed status transitions for a doctor.
     * Terminal statuses (completed, cancelled, no_show) are not in this map.
     */
    private const TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled', 'no_show'],
    ];

    public function index(): View
    {
        $doctor = Auth::user()->doctor;

        $upcoming = Appointment::where('doctor_id', $doctor->id)
            ->upcoming()
            ->with('patient.user')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $historical = Appointment::where('doctor_id', $doctor->id)
            ->historical()
            ->with('patient.user')
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10);

        return view('doctor.appointments.index', compact('upcoming', 'historical'));
    }

    public function show(Appointment $appointment): View
    {
        abort_if($appointment->doctor_id !== Auth::user()->doctor->id, 403);

        $appointment->load('patient.user');

        $allowedTransitions = self::TRANSITIONS[$appointment->status] ?? [];

        return view('doctor.appointments.show', compact('appointment', 'allowedTransitions'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_if($appointment->doctor_id !== Auth::user()->doctor->id, 403);

        $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $current = $appointment->status;
        $new = $request->status;

        $allowed = self::TRANSITIONS[$current] ?? [];

        if (! in_array($new, $allowed, true)) {
            return back()->with('error', "Cannot move from '{$current}' to '{$new}'.");
        }

        $appointment->update(['status' => $new]);

        $message = match ($new) {
            'confirmed' => 'Appointment confirmed. The patient will be notified.',
            'completed' => 'Appointment marked as completed.',
            'cancelled' => 'Appointment cancelled.',
            'no_show'   => 'Appointment marked as no-show.',
            default     => 'Status updated.',
        };

        return back()->with('success', $message);
    }
}
