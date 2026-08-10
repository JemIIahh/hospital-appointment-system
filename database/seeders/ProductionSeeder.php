<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hospital.test'],
            [
                'name'     => 'System Administrator',
                'email'    => 'admin@hospital.test',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '+234-800-000-0000',
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $admin->markEmailAsVerified();
        }

        $doctors = [
            ['name' => 'Dr. Adebayo Ogunlesi',   'email' => 'adebayo.ogunlesi@hospital.test', 'department' => 'Cardiology',       'specialization' => 'Interventional Cardiologist', 'fee' => 200.00],
            ['name' => 'Dr. Chioma Eze',          'email' => 'chioma.eze@hospital.test',       'department' => 'Pediatrics',       'specialization' => 'Pediatrician',                'fee' => 150.00],
            ['name' => 'Dr. Emeka Nwosu',         'email' => 'emeka.nwosu@hospital.test',      'department' => 'Orthopedics',      'specialization' => 'Orthopedic Surgeon',          'fee' => 250.00],
            ['name' => 'Dr. Funmilayo Adeyemi',   'email' => 'funmi.adeyemi@hospital.test',    'department' => 'Dermatology',      'specialization' => 'Dermatologist',               'fee' => 180.00],
            ['name' => 'Dr. Ibrahim Bello',       'email' => 'ibrahim.bello@hospital.test',    'department' => 'Neurology',        'specialization' => 'Neurologist',                 'fee' => 300.00],
            ['name' => 'Dr. Ngozi Okonkwo',       'email' => 'ngozi.okonkwo@hospital.test',    'department' => 'General Medicine',  'specialization' => 'General Practitioner',        'fee' => 100.00],
            ['name' => 'Dr. Oluwaseun Akinwale',  'email' => 'seun.akinwale@hospital.test',    'department' => 'Emergency',        'specialization' => 'Emergency Physician',         'fee' => 220.00],
        ];

        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($doctors as $data) {
            $department = Department::where('name', $data['department'])->first();
            if (!$department) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'doctor',
                    'phone'    => fake()->phoneNumber(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->markEmailAsVerified();
            }

            $doctor = Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id'    => $department->id,
                    'specialization'   => $data['specialization'],
                    'license_number'   => 'MED-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                    'consultation_fee' => $data['fee'],
                    'bio'              => "Dr. {$user->name} is an experienced {$data['specialization']} with years of clinical practice in {$data['department']}.",
                ]
            );

            if ($doctor->wasRecentlyCreated) {
                foreach ($weekdays as $day) {
                    DoctorSchedule::firstOrCreate(
                        ['doctor_id' => $doctor->id, 'day_of_week' => $day],
                        [
                            'start_time'    => '09:00:00',
                            'end_time'      => '17:00:00',
                            'slot_duration' => 30,
                        ]
                    );
                }
            }
        }
    }
}