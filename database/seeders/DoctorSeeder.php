<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all()->keyBy('name');

        $specializations = [
            'Cardiology'       => ['Interventional Cardiologist', 'Cardiac Surgeon', 'Electrophysiologist'],
            'Pediatrics'       => ['Pediatrician', 'Pediatric Surgeon', 'Neonatologist'],
            'Orthopedics'      => ['Orthopedic Surgeon', 'Sports Medicine', 'Spine Specialist'],
            'Dermatology'      => ['Dermatologist', 'Cosmetic Dermatologist'],
            'Neurology'        => ['Neurologist', 'Neurosurgeon'],
            'General Medicine' => ['General Practitioner', 'Internal Medicine'],
            'Emergency'        => ['Emergency Physician', 'Trauma Specialist'],
        ];

        $departmentNames = $departments->keys()->all();
        $doctorUsers = User::where('role', 'doctor')->orderBy('id')->get();

        foreach ($doctorUsers as $index => $user) {
            $deptName = $departmentNames[$index % count($departmentNames)];
            $dept = $departments[$deptName];
            $specOptions = $specializations[$deptName];

            Doctor::create([
                'user_id'          => $user->id,
                'department_id'    => $dept->id,
                'specialization'   => fake()->randomElement($specOptions),
                'license_number'   => 'MED-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                'consultation_fee' => fake()->randomFloat(2, 50, 300),
                'bio'              => fake()->paragraph(),
            ]);
        }
    }
}
