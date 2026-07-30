<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMailable;

class OTPService
{
    /**
     * Generate and send an OTP verification email.
     */
    public function generateAndSendOTP(string $email): bool
    {
        // 1. Generate a 6-digit code
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = now()->addMinutes(10);

        // 2. Write to database
        DB::table('otp_verifications')->insert([
            'email' => $email,
            'otp_code' => $otpCode,
            'expires_at' => $expiry,
            'verified' => false,
            'created_at' => now(),
        ]);

        // 3. Dispatch the OTP email
        try {
            Mail::to($email)->send(new OTPMailable($otpCode));
            return true;
        } catch (\Exception $e) {
            // Log the error and allow execution
            logger()->error("Failed sending OTP to $email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate the provided OTP code.
     */
    public function verifyOTP(string $email, string $otpCode): bool
    {
        $verification = DB::table('otp_verifications')
            ->where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($verification) {
            DB::table('otp_verifications')
                ->where('id', $verification->id)
                ->update([
                    'verified' => true,
                ]);
            return true;
        }

        return false;
    }
}
