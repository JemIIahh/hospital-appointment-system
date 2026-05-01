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

class BookingConflictTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a doctor with a Mon-Fri 9-5 schedule and two patients ready to book.
     *
     * @return array{doctor:Doctor, p1:User, p2:User, date:string}
     */
    private function setupBookingScenario(): array
    {
        $department = Department::factory()->create();
        $doctorUser = User::factory()->doctor()->create();
        $doctor = Doctor::factory()->create([
            'user_id'       => $doctorUser->id,
            'department_id' => $department->id,
        ]);
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            DoctorSchedule::create([
                'doctor_id'     => $doctor->id,
                'day_of_week'   => $day,
                'start_time'    => '09:00:00',
                'end_time'      => '17:00:00',
                'slot_duration' => 30,
            ]);
        }

        $p1 = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $p1->id]);
        $p2 = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $p2->id]);

        // Find next weekday (skip weekends)
        $date = Carbon::tomorrow();
        while ($date->isWeekend()) {
            $date->addDay();
        }

        return ['doctor' => $doctor, 'p1' => $p1->fresh('patient'), 'p2' => $p2->fresh('patient'), 'date' => $date->toDateString()];
    }

    public function test_first_patient_can_book_a_slot(): void
    {
        ['doctor' => $doctor, 'p1' => $p1, 'date' => $date] = $this->setupBookingScenario();

        $response = $this->actingAs($p1)->post('/patient/appointments', [
            'doctor_id'        => $doctor->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
            'reason'           => 'Routine checkup',
        ]);

        $response->assertRedirect(route('patient.appointments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_second_patient_cannot_book_same_slot(): void
    {
        ['doctor' => $doctor, 'p1' => $p1, 'p2' => $p2, 'date' => $date] = $this->setupBookingScenario();

        $payload = [
            'doctor_id'        => $doctor->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
            'reason'           => 'Conflict test',
        ];

        $this->actingAs($p1)->post('/patient/appointments', $payload);

        $response = $this->actingAs($p2)->post('/patient/appointments', $payload);
        $response->assertSessionHas('error');

        // Only the first booking exists.
        $this->assertEquals(1, Appointment::count());
    }

    public function test_cancelled_slot_can_be_rebooked(): void
    {
        ['doctor' => $doctor, 'p1' => $p1, 'p2' => $p2, 'date' => $date] = $this->setupBookingScenario();

        $payload = [
            'doctor_id'        => $doctor->id,
            'appointment_date' => $date,
            'appointment_time' => '11:00:00',
            'reason'           => 'First booking',
        ];

        $this->actingAs($p1)->post('/patient/appointments', $payload);
        $first = Appointment::first();

        // p1 cancels
        $this->actingAs($p1)->delete("/patient/appointments/{$first->id}");
        $this->assertSame('cancelled', $first->fresh()->status);

        // p2 books the same slot — should succeed because slot service
        // excludes cancelled appointments.
        $response = $this->actingAs($p2)->post('/patient/appointments', $payload);
        $response->assertSessionHas('success');

        $this->assertEquals(2, Appointment::count());
        $this->assertEquals(1, Appointment::where('status', 'pending')->count());
        $this->assertEquals(1, Appointment::where('status', 'cancelled')->count());
    }

    public function test_booking_in_the_past_is_rejected(): void
    {
        ['doctor' => $doctor, 'p1' => $p1] = $this->setupBookingScenario();

        $response = $this->actingAs($p1)->post('/patient/appointments', [
            'doctor_id'        => $doctor->id,
            'appointment_date' => Carbon::yesterday()->toDateString(),
            'appointment_time' => '10:00:00',
            'reason'           => 'Should fail',
        ]);

        $response->assertSessionHasErrors(['appointment_date']);
        $this->assertEquals(0, Appointment::count());
    }
}
