<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    private const WRITABLE_STATUSES = ['confirmed', 'completed'];

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authoriseWrite($appointment);

        if ($appointment->prescription) {
            return back()->with('error', 'A prescription already exists for this appointment. Use Edit to revise it.');
        }

        $data = $this->validatePrescription($request);

        DB::transaction(function () use ($appointment, $data) {
            $prescription = Prescription::create([
                'appointment_id'       => $appointment->id,
                'general_instructions' => $data['general_instructions'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $prescription->items()->create($item);
            }
        });

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Prescription saved.');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authoriseWrite($appointment);

        $prescription = $appointment->prescription;
        abort_unless($prescription, 404, 'No prescription exists yet — create one first.');

        $data = $this->validatePrescription($request);

        DB::transaction(function () use ($prescription, $data) {
            $prescription->update([
                'general_instructions' => $data['general_instructions'] ?? null,
            ]);

            // Replace all items: simpler than diffing, safe due to FK cascade.
            $prescription->items()->delete();
            foreach ($data['items'] as $item) {
                $prescription->items()->create($item);
            }
        });

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('success', 'Prescription updated.');
    }

    public function downloadPdf(Prescription $prescription): Response
    {
        abort_if($prescription->appointment->doctor_id !== Auth::user()->doctor->id, 403);

        return $this->renderPdf($prescription);
    }

    private function validatePrescription(Request $request): array
    {
        return $request->validate([
            'general_instructions'    => ['nullable', 'string', 'max:5000'],
            'items'                   => ['required', 'array', 'min:1', 'max:30'],
            'items.*.medication_name' => ['required', 'string', 'max:255'],
            'items.*.dosage'          => ['required', 'string', 'max:100'],
            'items.*.frequency'       => ['required', 'string', 'max:100'],
            'items.*.duration'        => ['required', 'string', 'max:100'],
        ]);
    }

    private function authoriseWrite(Appointment $appointment): void
    {
        abort_if($appointment->doctor_id !== Auth::user()->doctor->id, 403);
        abort_if(
            ! in_array($appointment->status, self::WRITABLE_STATUSES, true),
            422,
            "Prescriptions can only be added to confirmed or completed appointments. Current status: {$appointment->status}."
        );
    }

    private function renderPdf(Prescription $prescription): Response
    {
        $prescription->load([
            'appointment.doctor.user',
            'appointment.doctor.department',
            'appointment.patient.user',
            'items',
        ]);

        $pdf = Pdf::loadView('pdfs.prescription', compact('prescription'))->setPaper('a4');

        return $pdf->download("prescription-{$prescription->id}.pdf");
    }
}
