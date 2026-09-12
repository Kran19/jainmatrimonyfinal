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

        // Seed default candidate user
        \App\Models\User::updateOrCreate(
            ['email' => 'user@jain.com'],
            [
                'profile_id' => 'JM-2026-0001',
                'full_name' => 'Rahul Jain',
                'email' => 'user@jain.com',
                'mobile' => '9876543210',
                'country_code' => '+91',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'are_you_digambar_jain' => 'Yes',
                'cast' => 'Digambar',
                'subcast' => 'Bisapanthi',
                'gender' => 'Male',
                'birth_date' => '1998-05-15',
                'birth_time' => '10:30 AM',
                'birth_place' => 'Indore',
                'native_place' => 'Indore',
                'gotra' => 'Kashyap',
                'mama_gotra' => 'Garg',
                'manglik' => 'No',
                'height' => "5'10\"",
                'weight' => '70 kg',
                'marital_status' => 'Never Married',
                'handicapped' => 'No',
                'higher_education' => 'B.Tech in Computer Science',
                'occupation' => 'Software Engineer',
                'company_name' => 'Tech Corp',
                'designation' => 'Senior Developer',
                'monthly_income' => 85000,
                'income_type' => 'Monthly',
                'permanent_address' => '123, MG Road, Indore, MP',
                'pin_code' => '452001',
                'current_address' => '123, MG Road, Indore, MP',
                'father_name' => 'Suresh Jain',
                'father_mobile' => '9876500001',
                'father_occupation' => 'Business',
                'mother_name' => 'Sunita Jain',
                'mother_mobile' => '9876500002',
                'mother_occupation' => 'Homemaker',
                'status' => 'approved',
                'verified' => true,
                'is_public' => true,
                'payment_status' => 'approved',
                'registration_source' => 'website',
                'approval_date' => now()->toDateString(),
                'expiry_date' => now()->addYear()->toDateString(),
            ]
        );
    }
}
