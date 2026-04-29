<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Cardiology',       'description' => 'Diagnosis and treatment of heart and circulatory conditions.'],
            ['name' => 'Pediatrics',       'description' => 'Medical care for infants, children, and adolescents.'],
            ['name' => 'Orthopedics',      'description' => 'Bones, joints, ligaments, tendons, and muscles.'],
            ['name' => 'Dermatology',      'description' => 'Conditions of the skin, hair, and nails.'],
            ['name' => 'Neurology',        'description' => 'Disorders of the brain, spinal cord, and nervous system.'],
            ['name' => 'General Medicine', 'description' => 'Primary care and general adult medicine.'],
            ['name' => 'Emergency',        'description' => 'Urgent and emergency medical services.'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
