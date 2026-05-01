<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(): View
    {
        $patientId = Auth::user()->patient->id;

        $prescriptions = Prescription::whereHas('appointment', fn ($q) => $q->where('patient_id', $patientId))
            ->with(['appointment.doctor.user', 'appointment.doctor.department', 'items'])
            ->latest('id')
            ->paginate(15);

        return view('patient.prescriptions.index', compact('prescriptions'));
    }

    public function show(Prescription $prescription): View
    {
        $this->authorise($prescription);

        $prescription->load([
            'appointment.doctor.user',
            'appointment.doctor.department',
            'items',
        ]);

        return view('patient.prescriptions.show', compact('prescription'));
    }

    public function downloadPdf(Prescription $prescription): Response
    {
        $this->authorise($prescription);

        $prescription->load([
            'appointment.doctor.user',
            'appointment.doctor.department',
            'appointment.patient.user',
            'items',
        ]);

        $pdf = Pdf::loadView('pdfs.prescription', compact('prescription'))->setPaper('a4');

        return $pdf->download("prescription-{$prescription->id}.pdf");
    }

    private function authorise(Prescription $prescription): void
    {
        abort_if($prescription->appointment->patient_id !== Auth::user()->patient->id, 403);
    }
}
