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

                // Option B: Wipe previous profile data for a 100% fresh-slate restart
                $user->gender = null;
                $user->birth_date = null;
                $user->birth_time = null;
                $user->birth_place = null;
                $user->marital_status = null;
                $user->gotra = null;
                $user->mama_gotra = null;
                $user->manglik = null;
                $user->height = null;
                $user->weight = null;
                $user->handicapped = null;
                $user->handicapped_details = null;
                $user->higher_education = null;
                $user->education_detail = null;
                $user->occupation = null;
                $user->company_name = null;
                $user->designation = null;
                $user->monthly_income = null;
                $user->native_place = null;
                $user->current_address = null;
                $user->father_name = null;
                $user->father_occupation = null;
                $user->father_income = null;
                $user->mother_name = null;
                $user->mother_occupation = null;
                $user->unmarried_brothers = 0;
                $user->married_brothers = 0;
                $user->unmarried_sisters = 0;
                $user->married_sisters = 0;
                $user->about_me = null;
                $user->partner_expectations = null;
                $user->profile_photo = null;
                $user->horoscope_photo = null;
                $user->id_proof_photo = null;
                $user->other_photos = null;
                $user->ref1_name = null;
                $user->ref1_relation = null;
                $user->ref1_mobile = null;
                $user->ref1_city = null;
                $user->ref2_name = null;
                $user->ref2_relation = null;
                $user->ref2_mobile = null;
                $user->ref2_city = null;
                $user->registration_step = 1;
                $user->approved_by = null;
                $user->approved_at = null;
                $user->payment_status = 'pending';
                $user->payment_transaction_id = null;
                $user->payment_screenshot = null;

                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'deleted_at')) {
                    $user->deleted_at = null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'delete_reason')) {
                    $user->delete_reason = null;
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
