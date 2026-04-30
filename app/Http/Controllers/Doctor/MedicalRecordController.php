<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Statuses where a medical record may be authored or edited.
     * pending = patient hasn't been seen; cancelled/no_show = no consultation happened.
     */
    private const WRITABLE_STATUSES = ['confirmed', 'completed'];

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorise($appointment);

        if ($appointment->medicalRecord) {
            return back()->with('error', 'A consultation note already exists for this appointment. Use Edit to revise it.');
        }

        $data = $request->validate([
            'diagnosis' => ['required', 'string', 'max:5000'],
            'notes'     => ['nullable', 'string', 'max:10000'],
        ]);

        MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'diagnosis'      => $data['diagnosis'],
            'notes'          => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Consultation notes saved.');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorise($appointment);

        $record = $appointment->medicalRecord;
        abort_unless($record, 404, 'No consultation note exists yet — create one first.');

        $data = $request->validate([
            'diagnosis' => ['required', 'string', 'max:5000'],
            'notes'     => ['nullable', 'string', 'max:10000'],
        ]);

        $record->update([
            'diagnosis' => $data['diagnosis'],
            'notes'     => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Consultation notes updated.');
    }

    private function authorise(Appointment $appointment): void
    {
        abort_if($appointment->doctor_id !== Auth::user()->doctor->id, 403);

        abort_if(
            ! in_array($appointment->status, self::WRITABLE_STATUSES, true),
            422,
            "Notes can only be added to confirmed or completed appointments. This one is {$appointment->status}."
        );
    }
}
