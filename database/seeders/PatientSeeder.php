<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patientUsers = User::where('role', 'patient')->get();

        foreach ($patientUsers as $user) {
            Patient::create([
                'user_id'           => $user->id,
                'date_of_birth'     => fake()->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
                'gender'            => fake()->randomElement(['male', 'female', 'other']),
                'blood_group'       => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'unknown']),
                'address'           => fake()->address(),
                'emergency_contact' => fake()->phoneNumber(),
            ]);
        }
    }
}
