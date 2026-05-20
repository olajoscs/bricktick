<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_payload_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Alex Builder',
            'email' => 'alex@example.com',
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonPath('message', 'Welcome to your dashboard.')
            ->assertJsonPath('user.name', 'Alex Builder')
            ->assertJsonPath('user.email', 'alex@example.com')
            ->assertJsonPath('stats.active_projects', 3)
            ->assertJsonPath('stats.pending_tasks', 12);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }
}
