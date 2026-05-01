<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{doctor:User, appt:Appointment} */
    private function appointmentInStatus(string $status): array
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
            'status'           => $status,
            'reason'           => 'Test',
        ]);

        return ['doctor' => $docUser->fresh('doctor'), 'appt' => $appt];
    }

    public function test_doctor_can_write_notes_on_confirmed_appointment(): void
    {
        ['doctor' => $doctor, 'appt' => $appt] = $this->appointmentInStatus('confirmed');

        $this->actingAs($doctor)
            ->post("/doctor/appointments/{$appt->id}/notes", [
                'diagnosis' => 'Mild hypertension',
                'notes'     => 'BP 145/92. Recommend low-sodium diet.',
            ])
            ->assertRedirect(route('doctor.appointments.show', $appt));

        $this->assertDatabaseHas('medical_records', [
            'appointment_id' => $appt->id,
            'diagnosis'      => 'Mild hypertension',
        ]);
    }

    public function test_doctor_cannot_write_notes_on_pending_appointment(): void
    {
        ['doctor' => $doctor, 'appt' => $appt] = $this->appointmentInStatus('pending');

        $this->actingAs($doctor)
            ->post("/doctor/appointments/{$appt->id}/notes", [
                'diagnosis' => 'Should fail',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('medical_records', 0);
    }

    public function test_doctor_cannot_write_notes_on_cancelled_appointment(): void
    {
        ['doctor' => $doctor, 'appt' => $appt] = $this->appointmentInStatus('cancelled');

        $this->actingAs($doctor)
            ->post("/doctor/appointments/{$appt->id}/notes", [
                'diagnosis' => 'Should fail',
            ])
            ->assertStatus(422);
    }

    public function test_diagnosis_is_required(): void
    {
        ['doctor' => $doctor, 'appt' => $appt] = $this->appointmentInStatus('completed');

        $this->actingAs($doctor)
            ->post("/doctor/appointments/{$appt->id}/notes", [
                'notes' => 'Notes only, no diagnosis',
            ])
            ->assertSessionHasErrors(['diagnosis']);
    }
}
