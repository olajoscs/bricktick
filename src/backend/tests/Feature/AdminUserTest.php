<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_users(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_user_cannot_access_admin_users(): void
    {
        $user = User::factory()->create([
            'has_admin' => false,
            'password' => 'secret',
        ]);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_and_view_user(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'secret',
        ]);

        $this->actingAs($admin)->get('/admin/users');

        $create = $this->post('/admin/users', [
            '_token' => session()->token(),
            'name' => 'Jane Builder',
            'email' => 'jane@example.com',
            'title' => 'Site Manager',
            'password' => 'secret-password',
        ]);

        $create->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Builder',
            'email' => 'jane@example.com',
            'title' => 'Site Manager',
            'has_admin' => false,
            'is_active' => true,
        ]);

        $user = User::query()->where('email', 'jane@example.com')->first();

        $show = $this->get('/admin/user?id='.$user->id);

        $show->assertOk()
            ->assertSee('Jane Builder')
            ->assertSee('jane@example.com')
            ->assertSee('Site Manager')
            ->assertSee($user->created_at->format('Y-m-d'));
    }

    public function test_admin_created_user_can_log_in_to_frontend(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'secret',
        ]);

        $this->actingAs($admin)->get('/admin/users');

        $this->post('/admin/users', [
            '_token' => session()->token(),
            'name' => 'Frontend User',
            'email' => 'frontend@example.com',
            'title' => 'Foreman',
            'password' => 'frontend-secret',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'frontend@example.com',
            'password' => 'frontend-secret',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_admin_can_update_and_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'Old name',
            'email' => 'old@example.com',
            'title' => 'Old title',
            'password' => 'old-password',
        ]);

        $this->actingAs($admin)->get('/admin/user?id='.$user->id);

        $update = $this->put('/admin/user', [
            '_token' => session()->token(),
            'id' => $user->id,
            'name' => 'Updated name',
            'email' => 'updated@example.com',
            'title' => 'Updated title',
            'is_active' => '0',
        ]);

        $update->assertRedirect(route('admin.users.show', ['id' => $user->id]));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated name',
            'email' => 'updated@example.com',
            'title' => 'Updated title',
            'is_active' => false,
        ]);

        $this->get('/admin/user?id='.$user->id);

        $delete = $this->delete('/admin/user', [
            '_token' => session()->token(),
            'id' => $user->id,
        ]);

        $delete->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/user?id='.$admin->id);

        $delete = $this->delete('/admin/user', [
            '_token' => session()->token(),
            'id' => $admin->id,
        ]);

        $delete->assertRedirect(route('admin.users.show', ['id' => $admin->id]));
        $delete->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/user?id='.$admin->id);

        $update = $this->put('/admin/user', [
            '_token' => session()->token(),
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'title' => $admin->title,
        ]);

        $update->assertRedirect(route('admin.users.show', ['id' => $admin->id]));
        $update->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_delete_other_admin_accounts(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create([
            'email' => 'other-admin@example.com',
        ]);

        $this->actingAs($admin)->get('/admin/user?id='.$otherAdmin->id);

        $delete = $this->delete('/admin/user', [
            '_token' => session()->token(),
            'id' => $otherAdmin->id,
        ]);

        $delete->assertRedirect(route('admin.users.show', ['id' => $otherAdmin->id]));
        $delete->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }
}
