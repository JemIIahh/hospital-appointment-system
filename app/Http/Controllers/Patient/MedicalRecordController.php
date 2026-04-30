<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(): View
    {
        $patientId = Auth::user()->patient->id;

        $records = MedicalRecord::whereHas('appointment', fn ($q) => $q->where('patient_id', $patientId))
            ->with(['appointment.doctor.user', 'appointment.doctor.department'])
            ->latest('id')
            ->paginate(15);

        return view('patient.records.index', compact('records'));
    }

    public function show(MedicalRecord $record): View
    {
        $record->load('appointment.doctor.user', 'appointment.doctor.department');

        abort_if($record->appointment->patient_id !== Auth::user()->patient->id, 403);

        return view('patient.records.show', compact('record'));
    }
}
