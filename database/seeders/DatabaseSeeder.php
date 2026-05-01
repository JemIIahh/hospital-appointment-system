<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
            DoctorScheduleSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
