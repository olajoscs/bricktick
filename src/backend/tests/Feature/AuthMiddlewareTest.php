<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_session_allows_unauthenticated_requests(): void
    {
        $response = $this->getJson('/api/auth/session');

        $response->assertOk()
            ->assertJsonPath('guest', true);
    }

    public function test_guest_session_rejects_authenticated_requests(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/session');

        $response->assertForbidden()
            ->assertJsonPath('message', 'Already authenticated.')
            ->assertJsonPath('redirect', '/');
    }

    public function test_login_rejects_authenticated_requests(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'their-password',
            'is_active' => true,
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/login', [
                'email' => 'user@example.com',
                'password' => 'their-password',
            ]);

        $response->assertForbidden()
            ->assertJsonPath('message', 'Already authenticated.')
            ->assertJsonPath('redirect', '/');
    }

    public function test_protected_routes_require_authentication(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');

        $response = $this->getJson('/api/dashboard');

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_auth_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Alex Builder',
            'email' => 'alex@example.com',
            'is_active' => true,
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonPath('authenticated', true)
            ->assertJsonPath('user.name', 'Alex Builder')
            ->assertJsonPath('user.email', 'alex@example.com');
    }
}
