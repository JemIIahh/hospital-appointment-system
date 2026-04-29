<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach (Doctor::all() as $doctor) {
            foreach ($weekdays as $day) {
                DoctorSchedule::create([
                    'doctor_id'     => $doctor->id,
                    'day_of_week'   => $day,
                    'start_time'    => '09:00:00',
                    'end_time'      => '17:00:00',
                    'slot_duration' => 30,
                ]);
            }
        }
    }
}
