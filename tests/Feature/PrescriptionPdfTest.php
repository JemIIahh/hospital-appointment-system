<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionPdfTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{patient:User, doctor:User, rx:Prescription} */
    private function setupPrescription(): array
    {
        $dept = Department::factory()->create();
        $docUser = User::factory()->doctor()->create();
        $doctor = Doctor::factory()->create([
            'user_id' => $docUser->id, 'department_id' => $dept->id,
        ]);
        $patientUser = User::factory()->patient()->create();
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);

        $appt = Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $doctor->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '10:00:00',
            'status'           => 'completed',
            'reason'           => 'PDF test',
        ]);

        $rx = Prescription::create([
            'appointment_id'       => $appt->id,
            'general_instructions' => 'Take with food.',
        ]);
        $rx->items()->create([
            'medication_name' => 'Amoxicillin',
            'dosage'          => '500 mg',
            'frequency'       => 'Three times daily',
            'duration'        => '7 days',
        ]);

        return ['patient' => $patientUser->fresh('patient'), 'doctor' => $docUser->fresh('doctor'), 'rx' => $rx];
    }

    public function test_patient_can_download_their_prescription_pdf(): void
    {
        ['patient' => $patient, 'rx' => $rx] = $this->setupPrescription();

        $response = $this->actingAs($patient)->get("/patient/prescriptions/{$rx->id}/pdf");

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_doctor_can_download_their_prescription_pdf(): void
    {
        ['doctor' => $doctor, 'rx' => $rx] = $this->setupPrescription();

        $response = $this->actingAs($doctor)->get("/doctor/prescriptions/{$rx->id}/pdf");

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_other_patient_cannot_download_prescription_pdf(): void
    {
        ['rx' => $rx] = $this->setupPrescription();

        $otherPatientUser = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $otherPatientUser->id]);

        $this->actingAs($otherPatientUser->fresh('patient'))
            ->get("/patient/prescriptions/{$rx->id}/pdf")
            ->assertForbidden();
    }
}
