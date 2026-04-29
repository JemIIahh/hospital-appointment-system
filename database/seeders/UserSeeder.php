<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'System Administrator',
            'email' => 'admin@hospital.test',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            User::factory()->doctor()->create([
                'email' => "doctor{$i}@hospital.test",
            ]);
        }

        for ($i = 1; $i <= 12; $i++) {
            User::factory()->patient()->create([
                'email' => "patient{$i}@hospital.test",
            ]);
        }
    }
}
