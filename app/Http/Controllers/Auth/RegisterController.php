<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', '!=', 'deleted')->whereNull('deleted_at');
                })
            ],
            'mobile' => [
                'required', 'string', 'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'mobile')->where(function ($query) {
                    return $query->where('status', '!=', 'deleted')->whereNull('deleted_at');
                })
            ],
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
     * Resend OTP verification email without resetting registration form.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('reg_data')) {
            return redirect()->route('register')->with('error', 'Session expired. Please complete the registration form again.');
        }

        $regData = session('reg_data');
        $email = $regData['email'];

        $sent = $this->otpService->generateAndSendOTP($email);

        if ($sent) {
            return redirect()->route('register.otp')->with('success', 'A new verification OTP has been sent to <strong>' . e($email) . '</strong>.');
        }

        return redirect()->route('register.otp')->with('error', 'Failed to resend OTP email. Please check your email configuration.');
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

            // Check if an account with this email or mobile exists (including soft-deleted records)
            $existingDeletedUser = User::withTrashed()
                ->where(function ($q) use ($regData) {
                    $q->where('email', $regData['email'])
                      ->orWhere('mobile', $regData['mobile']);
                })
                ->orderBy('id', 'desc')
                ->first();

            // Ensure registration_count and deletion_count columns exist dynamically
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'registration_count')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->integer('registration_count')->default(1);
                });
                \Illuminate\Support\Facades\DB::table('users')->whereNull('registration_count')->update(['registration_count' => 1]);
            }

            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'deletion_count')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->integer('deletion_count')->default(0);
                });
                \Illuminate\Support\Facades\DB::table('users')->whereNull('deletion_count')->update(['deletion_count' => 0]);
            }

            if ($existingDeletedUser) {
                $user = $existingDeletedUser;
                $user->full_name = $regData['full_name'];
                $user->email = $regData['email'];
                $user->mobile = $regData['mobile'];
                $user->password_hash = $hashedPassword;
                $user->status = 'account_approved';
                $user->is_public = false; // Requires completing wizard and admin approval to go live
                $user->registration_count = intval($user->registration_count ?? 1) + 1;
                // Preserve deletion_count

                // Option B: Wipe previous profile data dynamically based on existing schema columns
                $resetFields = [
                    'gender' => null,
                    'birth_date' => null,
                    'birth_time' => null,
                    'birth_place' => null,
                    'marital_status' => null,
                    'gotra' => null,
                    'mama_gotra' => null,
                    'manglik' => null,
                    'height' => null,
                    'weight' => null,
                    'handicapped' => null,
                    'handicapped_details' => null,
                    'higher_education' => null,
                    'education_detail' => null,
                    'occupation' => null,
                    'company_name' => null,
                    'designation' => null,
                    'monthly_income' => null,
                    'native_place' => null,
                    'current_address' => null,
                    'father_name' => null,
                    'father_occupation' => null,
                    'father_income' => null,
                    'mother_name' => null,
                    'mother_occupation' => null,
                    'unmarried_brothers' => 0,
                    'married_brothers' => 0,
                    'unmarried_sisters' => 0,
                    'married_sisters' => 0,
                    'about_me' => null,
                    'partner_expectations' => null,
                    'profile_photo' => null,
                    'horoscope_photo' => null,
                    'id_proof_photo' => null,
                    'other_photos' => null,
                    'ref1_name' => null,
                    'ref1_relation' => null,
                    'ref1_mobile' => null,
                    'ref1_city' => null,
                    'ref2_name' => null,
                    'ref2_relation' => null,
                    'ref2_mobile' => null,
                    'ref2_city' => null,
                    'registration_step' => 1,
                    'approved_by' => null,
                    'approved_at' => null,
                    'payment_status' => 'pending',
                    'payment_transaction_id' => null,
                    'payment_screenshot' => null,
                    'deleted_at' => null,
                    'delete_reason' => null,
                ];

                foreach ($resetFields as $column => $value) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', $column)) {
                        $user->{$column} = $value;
                    }
                }
            } else {
                $user = new User();
                $user->full_name = $regData['full_name'];
                $user->email = $regData['email'];
                $user->mobile = $regData['mobile'];
                $user->password_hash = $hashedPassword;
                $user->status = 'account_approved';
                $user->is_public = false; // Requires completing wizard and admin approval to go live
                $user->registration_count = 1;
                $user->deletion_count = 0;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'has_set_password')) {
                $user->has_set_password = true;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'registration_source')) {
                $user->registration_source = 'website';
            }

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
