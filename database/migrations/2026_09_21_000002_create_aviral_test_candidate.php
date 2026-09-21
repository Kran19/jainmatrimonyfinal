<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $now = now();
        $userData = [
            'profile_id'            => 'JDM112233',
            'full_name'             => 'Aviral Jain',
            'email'                 => 'aviral@jain.com',
            'mobile'                => '9924114010',
            'country_code'          => '+91',
            'password_hash'         => Hash::make('12345678'),
            'are_you_digambar_jain' => 'Yes',
            'cast'                  => 'Digambar Jain',
            'subcast'               => 'Khandelwal',
            'gender'                => 'Male',
            'birth_date'            => '1997-04-12',
            'birth_time'            => '09:15:00',
            'birth_place'           => 'Jaipur',
            'native_place'          => 'Jaipur, Rajasthan',
            'gotra'                 => 'Kashyap',
            'mama_gotra'            => 'Garg',
            'manglik'               => 'No',
            'height'                => "5' 9\"",
            'weight'                => '68 kg',
            'marital_status'        => 'Never Married',
            'handicapped'           => 'No',
            'higher_education'      => 'B.Tech & MBA',
            'occupation'            => 'Job',
            'company_name'          => 'Infosys Technologies',
            'designation'           => 'Senior Consultant',
            'monthly_income'        => 95000,
            'income_type'           => 'Monthly',
            'permanent_address'     => 'B-12, Vaishali Nagar, Jaipur, Rajasthan',
            'pin_code'              => '302021',
            'current_address'       => 'B-12, Vaishali Nagar, Jaipur, Rajasthan',
            'father_name'           => 'Mahendra Jain',
            'father_mobile'         => '9829011111',
            'father_occupation'     => 'Business',
            'father_income'         => 1500000,
            'mother_name'           => 'Rekha Jain',
            'mother_mobile'         => '9829022222',
            'mother_occupation'     => 'Homemaker',
            'brothers'              => 1,
            'brothers_married'      => 0,
            'brothers_unmarried'    => 1,
            'sisters'               => 0,
            'mandir_name'           => 'Shri Digambar Jain Mandir, Vaishali Nagar',
            'mandir_address'        => 'Vaishali Nagar, Jaipur',
            'mandir_pincode'        => '302021',
            'ref1_name'             => 'Prakash Jain',
            'ref1_mobile'           => '9829033333',
            'ref1_relation'         => 'Uncle',
            'ref2_name'             => 'Ramesh Chhabra',
            'ref2_mobile'           => '9829044444',
            'ref2_relation'         => 'Family Friend',
            'languages'             => 'Hindi, English',
            'hobbies'               => 'Cricket, Reading, Photography',
            'partner_preference'    => 'Seeking an educated, vegetarian, and cultured life partner from Digambar Jain Samaj.',
            'status'                => 'pending',
            'is_approved'           => 0,
            'verified'              => 1,
            'is_public'             => 0,
            'payment_status'        => 'approved',
            'registration_source'   => 'website',
            'registration_step'     => 4,
            'updated_at'            => $now,
        ];

        // Filter out fields that might not exist in this database schema version
        $validData = [];
        foreach ($userData as $col => $val) {
            if (Schema::hasColumn('users', $col)) {
                $validData[$col] = $val;
            }
        }

        if (Schema::hasColumn('users', 'has_set_password')) {
            $validData['has_set_password'] = 1;
        }
        if (Schema::hasColumn('users', 'filled_by')) {
            $validData['filled_by'] = 'Self';
        }

        $existing = DB::table('users')->where('email', 'aviral@jain.com')->first();
        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update($validData);
        } else {
            if (Schema::hasColumn('users', 'created_at')) {
                $validData['created_at'] = $now;
            }
            DB::table('users')->insert($validData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe down
    }
};
