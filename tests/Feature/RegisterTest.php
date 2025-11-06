<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can register with valid data.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Akun berhasil dibuat! Silakan login.');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    /**
     * Test registration fails with duplicate email.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $userData = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', [
            'name' => 'New User',
            'email' => 'existing@example.com',
        ]);
    }

    /**
     * Test registration fails with invalid email format.
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test registration fails with short password.
     */
    public function test_registration_fails_with_short_password(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test registration fails with password confirmation mismatch.
     */
    public function test_registration_fails_with_password_mismatch(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test registration fails with missing required fields.
     */
    public function test_registration_fails_with_missing_fields(): void
    {
        $userData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Test register form can be accessed.
     */
    public function test_register_form_can_be_accessed(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }
}
