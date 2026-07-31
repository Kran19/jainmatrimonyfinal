<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Show form to request password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send password reset link to user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find a user with that email address.');
        }

        // Generate a secure token
        $token = Str::random(60);

        // Delete any old resets for this email
        DB::table('password_resets')->where('email', $email)->delete();

        // Insert new reset record
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token, // Stored in plain text since Core PHP compares it directly
            'created_at' => now(),
        ]);

        // Generate link
        $resetLink = route('password.reset', ['token' => $token]);

        try {
            Mail::send([], [], function ($message) use ($email, $user, $resetLink) {
                $message->to($email)
                    ->subject('Password Reset Request')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2>Password Reset Request</h2>
                            <p>Hello " . htmlspecialchars($user->full_name) . ",</p>
                            <p>We received a request to reset your password for your Digambar Jain Parichay account.</p>
                            <p>Please click the button below to reset your password. This link will expire in 1 hour.</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' style='background-color: #1E3A5F; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                            </p>
                            <p>If the button doesn't work, copy and paste this link into your browser:</p>
                            <p><a href='{$resetLink}'>{$resetLink}</a></p>
                            <p>If you did not request this, please ignore this email.</p>
                            <hr>
                            <p style='font-size: 12px; color: #666;'>Regards,<br>Digambar Jain Parichay Team</p>
                        </div>
                    ");
            });

            return back()->with('success', 'A password reset link has been sent to your email address.');
        } catch (\Exception $e) {
            logger()->error("Password reset mail failed: " . $e->getMessage());
            return back()->with('error', 'There was a problem sending the email. Please try again later.');
        }
    }

    /**
     * Show form to reset password.
     */
    public function showResetForm(Request $request, $token)
    {
        // Validate if token exists and is valid (within 1 hour)
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$reset) {
            return redirect()->route('password.request')
                ->with('error', 'This password reset link is invalid or has expired. Please request a new one.');
        }

        return view('auth.passwords.reset', ['token' => $token, 'email' => $reset->email]);
    }

    /**
     * Perform the password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$reset) {
            return back()->with('error', 'This password reset link has expired or is invalid.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $newHash = Hash::make($request->password);

        $updatePayload = [
            'password_hash' => $newHash,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'has_set_password')) {
            $updatePayload['has_set_password'] = true;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'password')) {
            $user->forceFill(['password' => $newHash]);
        }

        $user->update($updatePayload);

        // Clean up resets
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Log out the user so they can log in with their new password
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset. You can now log in.');
    }
}
