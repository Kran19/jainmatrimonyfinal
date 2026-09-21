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

        // Seed test candidate Krina
        \App\Models\User::updateOrCreate(
            ['email' => 'krina@jain.com'],
            [
                'profile_id' => 'JDM998877',
                'full_name' => 'Krina Jain',
                'email' => 'krina@jain.com',
                'mobile' => '9898989898',
                'country_code' => '+91',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'has_set_password' => 1,
                'are_you_digambar_jain' => 'Yes',
                'cast' => 'Digambar Jain',
                'subcast' => 'Khandelwal',
                'gender' => 'Female',
                'birth_date' => '1999-08-10',
                'birth_time' => '10:30:00',
                'birth_place' => 'Ahmedabad',
                'native_place' => 'Ahmedabad',
                'gotra' => 'Kashyap',
                'mama_gotra' => 'Garg',
                'manglik' => 'No',
                'height' => "5' 4\"",
                'weight' => '52 kg',
                'marital_status' => 'Never Married',
                'handicapped' => 'No',
                'higher_education' => 'B.E. Computer Engineering',
                'occupation' => 'Job',
                'company_name' => 'Software Solutions',
                'designation' => 'Software Engineer',
                'monthly_income' => 75000,
                'income_type' => 'Monthly',
                'permanent_address' => 'Navrangpura, Ahmedabad, Gujarat',
                'pin_code' => '380009',
                'current_address' => 'Navrangpura, Ahmedabad, Gujarat',
                'father_name' => 'Rajesh Jain',
                'father_mobile' => '9898900001',
                'father_occupation' => 'Business',
                'father_income' => 1200000,
                'mother_name' => 'Sunita Jain',
                'mother_mobile' => '9898900002',
                'mother_occupation' => 'Homemaker',
                'brothers' => 1,
                'brothers_married' => 0,
                'brothers_unmarried' => 1,
                'sisters' => 0,
                'mandir_name' => 'Shri Digambar Jain Mandir',
                'mandir_address' => 'Navrangpura, Ahmedabad',
                'mandir_pincode' => '380009',
                'ref1_name' => 'Mahesh Jain',
                'ref1_mobile' => '9898900011',
                'ref1_relation' => 'Uncle',
                'ref2_name' => 'Suresh Shah',
                'ref2_mobile' => '9898900012',
                'ref2_relation' => 'Family Friend',
                'languages' => 'Hindi, Gujarati, English',
                'hobbies' => 'Reading, Music, Traveling',
                'partner_preference' => 'Looking for a cultured and educated life partner from Digambar Jain Samaj.',
                'status' => 'approved',
                'is_approved' => 1,
                'verified' => 1,
                'is_public' => 1,
                'payment_status' => 'approved',
                'registration_source' => 'website',
                'registration_step' => 4,
                'approval_date' => now()->toDateString(),
                'expiry_date' => now()->addYear()->toDateString(),
                'approved_at' => now(),
            ]
        );
    }
}
