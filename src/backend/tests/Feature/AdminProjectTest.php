<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_projects(): void
    {
        $response = $this->get('/admin/projects');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_user_cannot_access_admin_projects(): void
    {
        $user = User::factory()->create([
            'has_admin' => false,
            'password' => 'secret',
        ]);

        $response = $this->actingAs($user)->get('/admin/projects');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_and_view_project(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'secret',
        ]);

        $this->actingAs($admin)->get('/admin/projects');

        $create = $this->post('/admin/projects', [
            '_token' => session()->token(),
            'name' => 'New build',
            'description' => 'A sample project.',
        ]);

        $create->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'New build',
            'description' => 'A sample project.',
        ]);

        $project = Project::query()->first();

        $show = $this->get('/admin/project?id='.$project->id);

        $show->assertOk()
            ->assertSee('New build')
            ->assertSee('A sample project.')
            ->assertSee($project->created_at->format('Y-m-d'));
    }

    public function test_admin_can_update_and_delete_project(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create([
            'name' => 'Old name',
            'description' => 'Old description',
        ]);

        $this->actingAs($admin)->get('/admin/project?id='.$project->id);

        $update = $this->put('/admin/project', [
            '_token' => session()->token(),
            'id' => $project->id,
            'name' => 'Updated name',
            'description' => 'Updated description',
        ]);

        $update->assertRedirect(route('admin.projects.show', ['id' => $project->id]));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated name',
            'description' => 'Updated description',
        ]);

        $this->get('/admin/project?id='.$project->id);

        $delete = $this->delete('/admin/project', [
            '_token' => session()->token(),
            'id' => $project->id,
        ]);

        $delete->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_admin_login_rejects_non_admin_user(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'secret',
            'has_admin' => false,
        ]);

        $this->get('/admin/login');

        $response = $this->post('/admin/login', [
            '_token' => session()->token(),
            'email' => 'user@example.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}
