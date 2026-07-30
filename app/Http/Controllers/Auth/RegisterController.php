<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show registration form (Step 1).
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle initial registration request (Step 1 submit).
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $regData = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ];

        // Store temporarily in session
        session(['reg_data' => $regData]);

        // Send OTP
        $sent = $this->otpService->generateAndSendOTP($request->email);

        if ($sent) {
            return redirect()->route('register.otp')->with('success', 'A verification OTP has been sent to your email.');
        }

        return back()->withInput()->with('error', 'Failed to send OTP email. Please verify your email configuration.');
    }

    /**
     * Show OTP verification form.
     */
    public function showOtpForm()
    {
        if (!session()->has('reg_data')) {
            return redirect()->route('register')->with('error', 'Please complete the registration form first.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP and complete registration.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        if (!session()->has('reg_data')) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }

        $regData = session('reg_data');

        $verified = $this->otpService->verifyOTP($regData['email'], $request->otp_code);

        if ($verified) {
            // The legacy DB 'users' table has a 'password' column with NOT NULL.
            // We use 'password_hash' for auth but must also supply 'password' to avoid SQL error.
            // Both receive the same hashed value.
            $hashedPassword = $regData['password'];

            $user = new User();
            $user->full_name    = $regData['full_name'];
            $user->email        = $regData['email'];
            $user->mobile       = $regData['mobile'];
            $user->password_hash = $hashedPassword;  // Used by Laravel Auth
            $user->status       = 'account_approved';
            $user->has_set_password = true;
            $user->registration_source = 'website';
            $user->is_public    = true;

            // Bypass Eloquent to also write the legacy 'password' column (NOT NULL in DB)
            // This prevents "Field 'password' doesn't have a default value" SQL error.
            // Once the DB column is made nullable (run run-patch.php), this line becomes harmless.
            $user->forceFill(['password' => $hashedPassword]);

            $user->save();

            // Clear session data
            session()->forget('reg_data');

            // Log user in
            Auth::guard('web')->login($user);

            return redirect()->route('registration.wizard')->with('success', 'Email verified successfully! Please complete your profile.');
        }

        return back()->with('error', 'Invalid or expired OTP. Please try again.');
    }
}
