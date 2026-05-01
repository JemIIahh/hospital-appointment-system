<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors  = Doctor::with('schedules')->get();
        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        // Track booked slots to avoid the unique-slot conflict that the
        // booking engine enforces in production.
        $taken = [];

        // Generate ~70 appointments across the last 60 days through 30 days
        // ahead, with a status mix weighted toward completed in the past
        // and pending/confirmed in the future.
        $generated = 0;
        $attempts  = 0;
        while ($generated < 70 && $attempts < 400) {
            $attempts++;

            $patient = $patients->random();
            $doctor  = $doctors->random();

            $offsetDays = random_int(-60, 30);
            $date = Carbon::today()->addDays($offsetDays);
            if ($date->isWeekend()) {
                continue;
            }

            // Pick a slot from the doctor's schedule for that day-of-week.
            $schedule = $doctor->schedules->firstWhere('day_of_week', strtolower($date->englishDayOfWeek));
            if (! $schedule) {
                continue;
            }

            // Slot times: 9:00 to 16:30, every 30 min
            $slot = sprintf('%02d:%02d:00', random_int(9, 16), [0, 30][random_int(0, 1)]);
            $key  = $doctor->id.'|'.$date->toDateString().'|'.$slot;
            if (isset($taken[$key])) {
                continue;
            }
            $taken[$key] = true;

            $status = $this->statusFor($offsetDays);
            $reason = fake()->randomElement([
                'Routine check-up', 'Persistent headache for a week',
                'Follow-up on previous diagnosis', 'Skin rash on left forearm',
                'Annual physical examination', 'Lower back pain',
                'Recurring chest discomfort', 'Vaccination consultation',
                'Dietary advice', 'Sleep difficulties for two months',
            ]);

            $appt = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'appointment_date' => $date->toDateString(),
                'appointment_time' => $slot,
                'status'           => $status,
                'reason'           => $reason,
                'created_at'       => $date->copy()->subDays(random_int(0, 14))->setTime(random_int(8, 18), random_int(0, 59)),
                'updated_at'       => now(),
            ]);

            // For completed appointments, attach a medical record and
            // (sometimes) a prescription so the dashboards have content.
            if ($status === 'completed') {
                MedicalRecord::create([
                    'appointment_id' => $appt->id,
                    'diagnosis'      => fake()->randomElement([
                        'Mild hypertension, stage 1', 'Common cold, viral',
                        'Tension-type headache', 'Seasonal allergic rhinitis',
                        'Mild gastritis', 'Lower back strain',
                        'Type 2 diabetes, well-controlled', 'Iron-deficiency anaemia',
                    ]),
                    'notes' => fake()->paragraph(),
                ]);

                if (random_int(1, 100) <= 70) { // 70% of completed get a prescription
                    $rx = Prescription::create([
                        'appointment_id'       => $appt->id,
                        'general_instructions' => fake()->randomElement([
                            'Take with food. Avoid alcohol while on antibiotics.',
                            'Plenty of fluids and rest. Return if symptoms worsen.',
                            'Reduce sodium intake. Daily walking, 30 minutes.',
                            null,
                        ]),
                    ]);

                    foreach (range(1, random_int(1, 3)) as $_) {
                        $rx->items()->create([
                            'medication_name' => fake()->randomElement([
                                'Amoxicillin', 'Paracetamol', 'Ibuprofen',
                                'Atorvastatin', 'Omeprazole', 'Loratadine',
                                'Metformin', 'Lisinopril', 'Vitamin D3',
                            ]),
                            'dosage'    => fake()->randomElement(['250 mg', '500 mg', '1000 mg', '10 mg', '20 mg']),
                            'frequency' => fake()->randomElement(['Once daily', 'Twice daily', 'Three times daily', 'Every 6 hours as needed']),
                            'duration'  => fake()->randomElement(['5 days', '7 days', '10 days', '14 days', '30 days']),
                        ]);
                    }
                }
            }

            $generated++;
        }
    }

    /**
     * Status weighting based on whether the appointment is in the past or future.
     */
    private function statusFor(int $offsetDays): string
    {
        if ($offsetDays > 0) {
            // Future: mostly pending or confirmed
            return fake()->randomElement(['pending', 'pending', 'confirmed', 'confirmed', 'confirmed']);
        }

        // Past: mix of completed (most common), some cancelled, occasional no_show
        return fake()->randomElement([
            'completed', 'completed', 'completed', 'completed', 'completed',
            'completed', 'completed', 'cancelled', 'cancelled', 'no_show',
        ]);
    }
}
