<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test Google OAuth redirect.
     */
    public function test_google_oauth_redirect(): void
    {
        $response = $this->get('/auth/google');

        $response->assertStatus(302); // Redirect status
        $response->assertRedirect(); // Should redirect to Google
    }

    /**
     * Test successful Google login for new user.
     */
    public function test_google_login_creates_new_user(): void
    {
        // Mock the Socialite Google user
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('123456789');
        $googleUser->shouldReceive('getName')->andReturn('John Doe');
        $googleUser->shouldReceive('getEmail')->andReturn('john.doe@gmail.com');

        // Mock Socialite driver
        $socialiteDriver = Mockery::mock();
        $socialiteDriver->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

        // Make the request
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');

        // Check that user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'google_id' => '123456789',
            'role' => 'user',
        ]);

        $user = User::where('email', 'john.doe@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    /**
     * Test Google login for existing user without Google ID.
     */
    public function test_google_login_updates_existing_user(): void
    {
        // Create existing user without Google ID
        $existingUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        // Mock the Socialite Google user
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('987654321');
        $googleUser->shouldReceive('getName')->andReturn('Jane Doe');
        $googleUser->shouldReceive('getEmail')->andReturn('jane.doe@gmail.com');

        // Mock Socialite driver
        $socialiteDriver = Mockery::mock();
        $socialiteDriver->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

        // Make the request
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');

        // Check that user was updated
        $existingUser->refresh();
        $this->assertEquals('987654321', $existingUser->google_id);
        $this->assertNotNull($existingUser->email_verified_at);
        $this->assertTrue(auth()->check());
        $this->assertEquals($existingUser->id, auth()->id());
    }

    /**
     * Test Google login for existing user with Google ID.
     */
    public function test_google_login_existing_user_with_google_id(): void
    {
        // Create existing user with Google ID
        $existingUser = User::create([
            'name' => 'Bob Smith',
            'email' => 'bob.smith@gmail.com',
            'password' => bcrypt('password123'),
            'google_id' => '555666777',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Mock the Socialite Google user
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('555666777');
        $googleUser->shouldReceive('getName')->andReturn('Bob Smith');
        $googleUser->shouldReceive('getEmail')->andReturn('bob.smith@gmail.com');

        // Mock Socialite driver
        $socialiteDriver = Mockery::mock();
        $socialiteDriver->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

        // Make the request
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');

        // Check that user is logged in
        $this->assertTrue(auth()->check());
        $this->assertEquals($existingUser->id, auth()->id());
    }

    /**
     * Test Google OAuth callback with exception.
     */
    public function test_google_oauth_callback_handles_exception(): void
    {
        // Mock Socialite to throw an exception
        $socialiteDriver = Mockery::mock();
        $socialiteDriver->shouldReceive('user')->andThrow(new \Exception('OAuth error'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($socialiteDriver);

        // Make the request
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['error' => 'Google login failed.']);
        $this->assertFalse(auth()->check());
    }
}
