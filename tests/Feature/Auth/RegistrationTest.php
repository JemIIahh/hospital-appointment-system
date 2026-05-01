<?php

namespace Tests\Feature\Auth;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register_as_patients(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'date_of_birth' => '1995-06-15',
            'gender' => 'female',
            'phone' => '+1234567890',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('patient', $user->role);

        // The Patient row was created in the same transaction.
        $this->assertNotNull($user->patient);
        $this->assertSame('female', $user->patient->gender);
        $this->assertSame('1995-06-15', $user->patient->date_of_birth->toDateString());
    }

    public function test_registration_requires_demographic_fields(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // missing date_of_birth + gender
        ]);

        $response->assertSessionHasErrors(['date_of_birth', 'gender']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
