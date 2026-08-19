<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add sub_admin to enum
        DB::statement("ALTER TABLE admins MODIFY COLUMN role ENUM('super_admin', 'admin', 'moderator', 'sub_admin') DEFAULT 'sub_admin'");

        // Disable foreign key checks temporarily in case admins are referenced
        Schema::disableForeignKeyConstraints();

        // Clear existing admins to reset to exactly 4 as requested
        DB::table('admins')->truncate();

        // Re-enable foreign keys
        Schema::enableForeignKeyConstraints();

        // Insert the 4 admins
        $password = Hash::make('password');
        
        DB::table('admins')->insert([
            [
                'name' => 'Admin 1 (Super Admin)',
                'email' => 'admin1@admin.com',
                'password_hash' => $password,
                'role' => 'super_admin',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin 2 (Super Admin)',
                'email' => 'admin2@admin.com',
                'password_hash' => $password,
                'role' => 'super_admin',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin 3 (Sub Admin)',
                'email' => 'admin3@admin.com',
                'password_hash' => $password,
                'role' => 'sub_admin',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin 4 (Sub Admin)',
                'email' => 'admin4@admin.com',
                'password_hash' => $password,
                'role' => 'sub_admin',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('admins')->truncate();
        Schema::enableForeignKeyConstraints();
        
        DB::table('admins')->insert([
            'name' => 'Default Admin',
            'email' => 'admin@admin.com',
            'password_hash' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
