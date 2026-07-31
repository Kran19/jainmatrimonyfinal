<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\EmailService;

class OTPService
{
    /**
     * Generate and send an OTP verification email with fallback delivery.
     */
    public function generateAndSendOTP(string $email): bool
    {
        // 1. Generate a 6-digit code
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = now()->addMinutes(10);

        // 2. Save OTP record to database
        DB::table('otp_verifications')->insert([
            'email' => $email,
            'otp_code' => $otpCode,
            'expires_at' => $expiry,
            'verified' => false,
            'created_at' => now(),
        ]);

        $subject = "Your Verification OTP - Jain Digambar Matrimony";
        $htmlContent = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #1E3A5F;'>Jain Digambar Matrimony</h2>
            <p>Hello,</p>
            <p>Your OTP verification code is:</p>
            <div style='font-size: 24px; font-weight: bold; background: #f3f4f6; padding: 12px 20px; border-radius: 6px; display: inline-block; color: #1E3A5F;'>
                $otpCode
            </div>
            <p>This code is valid for 10 minutes.</p>
            <p>Best regards,<br>Digambar Jain Matrimony Committee</p>
        </body>
        </html>";

        return EmailService::sendHtml($email, $subject, $htmlContent);
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
