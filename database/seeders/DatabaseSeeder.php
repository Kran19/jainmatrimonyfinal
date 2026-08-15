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
        // Seed default admin
        \App\Models\Admin::updateOrCreate(
            ['email' => 'admin@jain.com'],
            [
                'name' => 'Admin',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('12344321'),
                'role' => 'super_admin',
                'status' => true,
            ]
        );

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
