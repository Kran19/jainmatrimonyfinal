<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test step 1 registration forms can be submitted, creating OTP and session data.
     */
    public function test_registration_step_1_submits_successfully()
    {
        $response = $this->post('/register', [
            'full_name' => 'Registration Candidate',
            'email' => 'candreg@example.com',
            'mobile' => '9998887776',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/register/verify-otp');
        $this->assertTrue(session()->has('reg_data'));
        $this->assertEquals('candreg@example.com', session('reg_data')['email']);

        // Assert OTP database record exists
        $this->assertTrue(
            DB::table('otp_verifications')->where('email', 'candreg@example.com')->exists()
        );
    }

    /**
     * Test correct OTP code completes user creation and logs the candidate in.
     */
    public function test_valid_otp_completes_registration()
    {
        // 1. Setup session registration data
        session(['reg_data' => [
            'full_name' => 'OTP Verified User',
            'email' => 'otpok@example.com',
            'mobile' => '9112223334',
            'password' => bcrypt('pass123'),
        ]]);

        // 2. Insert valid OTP token
        DB::table('otp_verifications')->insert([
            'email' => 'otpok@example.com',
            'otp_code' => '555555',
            'expires_at' => now()->addMinutes(10),
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Post OTP submission
        $response = $this->post('/register/verify-otp', [
            'otp_code' => '555555',
        ]);

        // 4. Assert redirections and DB records
        $response->assertRedirect('/registration-wizard');
        $this->assertTrue(
            User::where('email', 'otpok@example.com')->exists()
        );

        $user = User::where('email', 'otpok@example.com')->first();
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertEquals('account_approved', $user->status);
    }

    /**
     * Test invalid OTP code fails validation and keeps user logged out.
     */
    public function test_invalid_otp_fails_registration()
    {
        session(['reg_data' => [
            'full_name' => 'Wrong OTP Candidate',
            'email' => 'otperr@example.com',
            'mobile' => '9443332221',
            'password' => bcrypt('pass123'),
        ]]);

        DB::table('otp_verifications')->insert([
            'email' => 'otperr@example.com',
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Post invalid code
        $response = $this->post('/register/verify-otp', [
            'otp_code' => '999999',
        ]);

        $response->assertSessionHas('error');
        $this->assertFalse(
            User::where('email', 'otperr@example.com')->exists()
        );
        $this->assertGuest('web');
    }
}
