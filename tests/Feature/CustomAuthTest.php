<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test candidate can log in using standard hashed passwords.
     */
    public function test_candidate_can_login_with_standard_hash()
    {
        $user = User::create([
            'full_name' => 'Test User Standard',
            'email' => 'standardtest@example.com',
            'mobile' => '9000000001',
            'password_hash' => Hash::make('secret123'),
            'status' => 'approved',
            'has_set_password' => true,
        ]);

        $response = $this->post('/login', [
            'login_input' => 'standardtest@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/profiles');
        $this->assertAuthenticatedAs($user, 'web');
    }

    /**
     * Test legacy candidates can log in using mobile number fallback when has_set_password is false.
     */
    public function test_candidate_can_login_with_legacy_mobile_fallback()
    {
        $user = User::create([
            'full_name' => 'Test User Legacy',
            'email' => 'legacytest@example.com',
            'mobile' => '9876543210',
            'password_hash' => 'legacy_placeholder',
            'status' => 'approved',
            'has_set_password' => false,
        ]);

        // Attempt login using mobile number
        $response = $this->post('/login', [
            'login_input' => 'legacytest@example.com',
            'password' => '9876543210',
        ]);

        $response->assertRedirect('/profiles');
        $this->assertAuthenticatedAs($user, 'web');
    }

    /**
     * Test candidate login fails with wrong password.
     */
    public function test_candidate_login_fails_with_invalid_credentials()
    {
        User::create([
            'full_name' => 'Test User Standard',
            'email' => 'wrongtest@example.com',
            'mobile' => '9000000002',
            'password_hash' => Hash::make('secret123'),
            'status' => 'approved',
            'has_set_password' => true,
        ]);

        $response = $this->post('/login', [
            'login_input' => 'wrongtest@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('login_input');
        $this->assertGuest('web');
    }
}
