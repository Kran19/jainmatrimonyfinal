<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMailable;

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

        // 3. Primary attempt: Send via Configured Hostinger SMTP
        try {
            Mail::mailer('smtp')->to($email)->send(new OTPMailable($otpCode));
            logger()->info("OTP sent successfully via SMTP to $email");
            return true;
        } catch (\Throwable $e) {
            logger()->error("SMTP OTP delivery failed for $email: " . $e->getMessage());
        }

        // 4. Fallback attempt: Send via PHP native mail() if SMTP fails on host
        try {
            $subject = "Your Verification OTP - Jain Digambar Matrimony";
            $htmlContent = "
            <html>
            <body style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2 style='color: #4f46e5;'>Jain Digambar Matrimony</h2>
                <p>Hello,</p>
                <p>Your OTP verification code is:</p>
                <div style='font-size: 24px; font-weight: bold; background: #f3f4f6; padding: 12px 20px; border-radius: 6px; display: inline-block; color: #1e1b4b;'>
                    $otpCode
                </div>
                <p>This code is valid for 10 minutes.</p>
                <p>Best regards,<br>Digambar Jain Matrimony Committee</p>
            </body>
            </html>";

            $headers = "MIME-Version: 1.0\r\n" .
                       "Content-type: text/html; charset=UTF-8\r\n" .
                       "From: Jain Digambar Matrimony <help@digambarjainparichay.com>\r\n" .
                       "Reply-To: help@digambarjainparichay.com\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            if (@mail($email, $subject, $htmlContent, $headers)) {
                logger()->info("Fallback PHP mail() succeeded for $email");
                return true;
            }
        } catch (\Throwable $e) {
            logger()->error("Fallback mail() failed for $email: " . $e->getMessage());
        }

        return false;
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
