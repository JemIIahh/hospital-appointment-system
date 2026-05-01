<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerRecordAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{p1:User, p2:User, appt:Appointment} */
    private function twoPatientsOneAppointment(): array
    {
        $dept = Department::factory()->create();
        $docUser = User::factory()->doctor()->create();
        $doctor = Doctor::factory()->create([
            'user_id' => $docUser->id, 'department_id' => $dept->id,
        ]);

        $p1 = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $p1->id]);
        $p2 = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $p2->id]);

        $appt = Appointment::create([
            'patient_id'       => $p1->fresh('patient')->patient->id,
            'doctor_id'        => $doctor->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '10:00:00',
            'status'           => 'completed',
            'reason'           => 'Cross-patient access test',
        ]);

        return ['p1' => $p1->fresh('patient'), 'p2' => $p2->fresh('patient'), 'appt' => $appt];
    }

    public function test_patient_cannot_view_another_patients_appointment(): void
    {
        ['p2' => $p2, 'appt' => $appt] = $this->twoPatientsOneAppointment();

        $this->actingAs($p2)
            ->get("/patient/appointments/{$appt->id}")
            ->assertForbidden();
    }

    public function test_patient_cannot_cancel_another_patients_appointment(): void
    {
        ['p2' => $p2, 'appt' => $appt] = $this->twoPatientsOneAppointment();

        $this->actingAs($p2)
            ->delete("/patient/appointments/{$appt->id}")
            ->assertForbidden();
    }

    public function test_patient_cannot_view_another_patients_medical_record(): void
    {
        ['p2' => $p2, 'appt' => $appt] = $this->twoPatientsOneAppointment();

        $record = MedicalRecord::create([
            'appointment_id' => $appt->id,
            'diagnosis'      => 'Test diagnosis',
            'notes'          => 'Test notes',
        ]);

        $this->actingAs($p2)
            ->get("/patient/records/{$record->id}")
            ->assertForbidden();
    }

    public function test_patient_cannot_view_another_patients_prescription(): void
    {
        ['p2' => $p2, 'appt' => $appt] = $this->twoPatientsOneAppointment();

        $rx = Prescription::create([
            'appointment_id'       => $appt->id,
            'general_instructions' => 'Test',
        ]);
        $rx->items()->create([
            'medication_name' => 'Paracetamol',
            'dosage'          => '500 mg',
            'frequency'       => 'Twice daily',
            'duration'        => '5 days',
        ]);

        $this->actingAs($p2)->get("/patient/prescriptions/{$rx->id}")->assertForbidden();
        $this->actingAs($p2)->get("/patient/prescriptions/{$rx->id}/pdf")->assertForbidden();
    }

    public function test_doctor_cannot_view_another_doctors_appointment(): void
    {
        ['appt' => $appt] = $this->twoPatientsOneAppointment();

        // Create a different doctor
        $dept = Department::factory()->create();
        $otherDocUser = User::factory()->doctor()->create();
        Doctor::factory()->create([
            'user_id' => $otherDocUser->id, 'department_id' => $dept->id,
        ]);

        $this->actingAs($otherDocUser->fresh('doctor'))
            ->get("/doctor/appointments/{$appt->id}")
            ->assertForbidden();
    }
}
