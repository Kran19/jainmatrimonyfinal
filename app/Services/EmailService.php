<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an HTML email with multi-tiered fallback (Laravel SMTP -> Socket SMTP -> Native PHP mail).
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $htmlContent
     * @return bool
     */
    public static function sendHtml(string $toEmail, string $subject, string $htmlContent): bool
    {
        $fromEmail = config('mail.from.address', 'help@digambarjainparichay.com');
        $fromName = config('mail.from.name', 'Jain Digambar Matrimony');

        // Tier 1: Primary Laravel Mailer with explicit from header
        try {
            Mail::html($htmlContent, function ($message) use ($toEmail, $subject, $fromEmail, $fromName) {
                $message->from($fromEmail, $fromName)
                    ->to($toEmail)
                    ->subject($subject);
            });
            logger()->info("EmailService: Sent via Laravel Mailer to {$toEmail}");
            return true;
        } catch (\Throwable $e) {
            logger()->error("EmailService: Primary Laravel Mailer failed for {$toEmail}: " . $e->getMessage());
        }

        // Tier 2: Direct Socket SMTP to Hostinger
        try {
            $host = env('MAIL_HOST', 'smtp.hostinger.com');
            $port = (int) env('MAIL_PORT', 465);
            $username = env('MAIL_USERNAME', 'help@digambarjainparichay.com');
            $password = env('MAIL_PASSWORD', 'King@0706');

            if (self::sendSocketSmtp($host, $port, $username, $password, $toEmail, $subject, $htmlContent, $fromName)) {
                logger()->info("EmailService: Sent via Direct Socket SMTP to {$toEmail}");
                return true;
            }
        } catch (\Throwable $e) {
            logger()->error("EmailService: Direct Socket SMTP failed for {$toEmail}: " . $e->getMessage());
        }

        // Tier 3: Native PHP mail() fallback
        try {
            $headers = "MIME-Version: 1.0\r\n" .
                       "Content-type: text/html; charset=UTF-8\r\n" .
                       "From: {$fromName} <{$fromEmail}>\r\n" .
                       "Reply-To: {$fromEmail}\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            if (@mail($toEmail, $subject, $htmlContent, $headers)) {
                logger()->info("EmailService: Sent via native PHP mail() to {$toEmail}");
                return true;
            }
        } catch (\Throwable $e) {
            logger()->error("EmailService: Native mail() failed for {$toEmail}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Raw socket SMTP sender for Hostinger / cPanel servers.
     */
    private static function sendSocketSmtp($host, $port, $username, $password, $toEmail, $subject, $htmlBody, $fromName): bool
    {
        $boundary = md5(time());
        $headers = "From: {$fromName} <{$username}>\r\n";
        $headers .= "Reply-To: {$username}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= strip_tags($htmlBody) . "\r\n\r\n";
        
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";
        
        $message .= "--{$boundary}--";

        $protocol = ($port == 465) ? 'ssl://' : '';
        $socket = @fsockopen($protocol . $host, $port, $errno, $errstr, 10);
        if (!$socket) {
            return false;
        }

        if (!self::serverParse($socket, "220")) { fclose($socket); return false; }

        fwrite($socket, "EHLO " . $host . "\r\n");
        if (!self::serverParse($socket, "250")) { fclose($socket); return false; }

        fwrite($socket, "AUTH LOGIN\r\n");
        if (!self::serverParse($socket, "334")) { fclose($socket); return false; }

        fwrite($socket, base64_encode($username) . "\r\n");
        if (!self::serverParse($socket, "334")) { fclose($socket); return false; }

        fwrite($socket, base64_encode($password) . "\r\n");
        if (!self::serverParse($socket, "235")) { fclose($socket); return false; }

        fwrite($socket, "MAIL FROM: <{$username}>\r\n");
        if (!self::serverParse($socket, "250")) { fclose($socket); return false; }

        fwrite($socket, "RCPT TO: <{$toEmail}>\r\n");
        if (!self::serverParse($socket, "250")) { fclose($socket); return false; }

        fwrite($socket, "DATA\r\n");
        if (!self::serverParse($socket, "354")) { fclose($socket); return false; }

        fwrite($socket, "Subject: " . $subject . "\r\n" . $headers . "\r\n" . $message . "\r\n.\r\n");
        if (!self::serverParse($socket, "250")) { fclose($socket); return false; }

        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        return true;
    }

    private static function serverParse($socket, $response): bool
    {
        $server_response = '';
        while (substr($server_response, 3, 1) != ' ') {
            if (!($server_response = fgets($socket, 256))) {
                return false;
            }
        }
        return (substr($server_response, 0, 3) == $response);
    }
}
