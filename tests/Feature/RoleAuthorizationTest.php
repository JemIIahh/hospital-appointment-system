<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function doctor(): User
    {
        $department = Department::factory()->create();
        $user = User::factory()->doctor()->create();
        Doctor::factory()->create([
            'user_id'        => $user->id,
            'department_id'  => $department->id,
        ]);

        return $user->fresh('doctor');
    }

    private function patient(): User
    {
        $user = User::factory()->patient()->create();
        Patient::factory()->create(['user_id' => $user->id]);

        return $user->fresh('patient');
    }

    public function test_admin_can_reach_admin_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_patient_cannot_reach_admin_routes(): void
    {
        $patient = $this->patient();

        foreach (['/admin/dashboard', '/admin/departments', '/admin/doctors', '/admin/reports'] as $url) {
            $this->actingAs($patient)->get($url)->assertForbidden();
        }
    }

    public function test_doctor_cannot_reach_admin_routes(): void
    {
        $doctor = $this->doctor();

        foreach (['/admin/dashboard', '/admin/departments', '/admin/doctors'] as $url) {
            $this->actingAs($doctor)->get($url)->assertForbidden();
        }
    }

    public function test_patient_cannot_reach_doctor_routes(): void
    {
        $this->actingAs($this->patient())
            ->get('/doctor/dashboard')
            ->assertForbidden();
    }

    public function test_doctor_cannot_reach_patient_routes(): void
    {
        $this->actingAs($this->doctor())
            ->get('/patient/dashboard')
            ->assertForbidden();
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        foreach (['/admin/dashboard', '/doctor/dashboard', '/patient/dashboard'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }
}
