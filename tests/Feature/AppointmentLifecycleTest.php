<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_lifecycle_book_confirm_complete(): void
    {
        // Setup
        $dept = Department::factory()->create();
        $docUser = User::factory()->doctor()->create();
        $doctor = Doctor::factory()->create([
            'user_id' => $docUser->id, 'department_id' => $dept->id,
        ]);
        foreach (['monday','tuesday','wednesday','thursday','friday'] as $day) {
            DoctorSchedule::create([
                'doctor_id'     => $doctor->id,
                'day_of_week'   => $day,
                'start_time'    => '09:00:00',
                'end_time'      => '17:00:00',
                'slot_duration' => 30,
            ]);
        }

        $patientUser = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $patientUser->id]);

        $date = Carbon::tomorrow();
        while ($date->isWeekend()) {
            $date->addDay();
        }

        // 1. Patient books → status = pending
        $this->actingAs($patientUser->fresh('patient'))->post('/patient/appointments', [
            'doctor_id'        => $doctor->id,
            'appointment_date' => $date->toDateString(),
            'appointment_time' => '14:00:00',
            'reason'           => 'Lifecycle test',
        ]);
        $appt = Appointment::firstWhere('doctor_id', $doctor->id);
        $this->assertSame('pending', $appt->status);

        // 2. Doctor confirms → status = confirmed
        $this->actingAs($docUser->fresh('doctor'))
            ->patch("/doctor/appointments/{$appt->id}/status", ['status' => 'confirmed'])
            ->assertSessionHas('success');
        $this->assertSame('confirmed', $appt->fresh()->status);

        // 3. Doctor completes → status = completed (terminal)
        $this->actingAs($docUser->fresh('doctor'))
            ->patch("/doctor/appointments/{$appt->id}/status", ['status' => 'completed'])
            ->assertSessionHas('success');
        $this->assertSame('completed', $appt->fresh()->status);

        // 4. Doctor cannot transition out of terminal state
        $this->actingAs($docUser->fresh('doctor'))
            ->patch("/doctor/appointments/{$appt->id}/status", ['status' => 'pending'])
            ->assertSessionHas('error');
        $this->assertSame('completed', $appt->fresh()->status);
    }

    public function test_patient_can_cancel_pending_appointment(): void
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
            'appointment_time' => '11:00:00',
            'status'           => 'pending',
            'reason'           => 'Test',
        ]);

        $this->actingAs($patientUser->fresh('patient'))
            ->delete("/patient/appointments/{$appt->id}")
            ->assertRedirect(route('patient.appointments.index'));

        $this->assertSame('cancelled', $appt->fresh()->status);
    }

    public function test_patient_cannot_cancel_completed_appointment(): void
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
            'appointment_date' => Carbon::yesterday()->toDateString(),
            'appointment_time' => '11:00:00',
            'status'           => 'completed',
            'reason'           => 'Test',
        ]);

        $this->actingAs($patientUser->fresh('patient'))
            ->delete("/patient/appointments/{$appt->id}")
            ->assertSessionHas('error');

        $this->assertSame('completed', $appt->fresh()->status);
    }
}
