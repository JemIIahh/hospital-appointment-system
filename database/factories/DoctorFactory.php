<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->doctor(),
            'department_id' => Department::factory(),
            'specialization' => fake()->randomElement([
                'General Practitioner',
                'Internal Medicine',
                'Cardiologist',
                'Pediatrician',
                'Dermatologist',
                'Orthopedic Surgeon',
                'Neurologist',
                'Emergency Physician',
            ]),
            'license_number' => 'MED-'.fake()->unique()->numerify('######'),
            'consultation_fee' => fake()->randomFloat(2, 50, 300),
            'bio' => fake()->paragraph(),
        ];
    }
}
