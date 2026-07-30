<?php

/**
 * Diagnostic Email Tester for Hostinger / SMTP
 * Access at: https://digambarjainparichay.com/testmail.php?email=your_email@gmail.com
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMailable;

$to = $_GET['email'] ?? 'help@digambarjainparichay.com';

echo "<pre style='font-family:monospace; background:#1e1e1e; color:#d4d4d4; padding:20px; font-size:14px; line-height:1.6;'>";
echo "=== SMTP MAIL DIAGNOSTIC TEST ===\n\n";
echo "Default Mailer : " . config('mail.default') . "\n";
echo "SMTP Host      : " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port      : " . config('mail.mailers.smtp.port') . "\n";
echo "Encryption     : " . config('mail.mailers.smtp.encryption') . "\n";
echo "Username       : " . config('mail.mailers.smtp.username') . "\n";
echo "From Address   : " . config('mail.from.address') . "\n";
echo "Sending test to: $to\n\n";

try {
    Mail::to($to)->send(new OTPMailable('123456'));
    echo "✅ SUCCESS: Test OTP email was sent successfully to $to!\n";
    echo "   Please check your Gmail inbox (or Spam/Junk folder).\n";
} catch (\Exception $e) {
    echo "❌ ERROR SENDING EMAIL:\n";
    echo htmlspecialchars($e->getMessage()) . "\n";
    echo "\nFull Stack Trace:\n" . htmlspecialchars($e->getTraceAsString()) . "\n";
}

echo "</pre>";
