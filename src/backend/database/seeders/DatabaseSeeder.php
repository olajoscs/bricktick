<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'has_admin' => true,
            'is_active' => true,
        ]);
    }
}
