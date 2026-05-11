<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_and_returns_jwt_payload(): void
    {
        $user = User::factory()->create([
            'email' => 'active@example.com',
            'password' => 'correct-password',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ])
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('expires_in', 60 * 60);

        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'real-password',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid credentials.');
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nobody@example.com',
            'password' => 'any-password',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid credentials.');
    }

    public function test_login_fails_when_user_is_inactive(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => 'their-password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'their-password',
        ]);

        $response->assertForbidden()
            ->assertJsonPath('message', 'This account has been deactivated.');
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'secret',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
