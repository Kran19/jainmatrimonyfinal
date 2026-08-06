<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show candidate login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle candidate login request.
     *
     * Login rules (matches Core PHP site behaviour):
     *  - Field: email OR mobile number
     *  - Password: either the user's chosen password (password_hash)
     *              OR their mobile number (legacy migrated accounts)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_input' => 'required|string',
            'password'    => 'required|string',
        ]);

        $loginInput = trim($request->login_input);
        $password   = $request->password;

        // Determine whether input is an email or mobile number
        $loginField = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        // Find the user by email or mobile
        $query = \App\Models\User::query();
        if (method_exists(\App\Models\User::class, 'withTrashed')) {
            $query->withTrashed();
        }
        $user = $query->where($loginField, $loginInput)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login_input' => ['These credentials do not match our records.'],
            ]);
        }

        // Block login if account has been deleted by user or administrator
        if (in_array($user->status, ['deleted', 'deactivated']) || !empty($user->deleted_at)) {
            throw ValidationException::withMessages([
                'login_input' => ['Your account has been deleted. This account has been permanently deleted and cannot be recovered. Please contact the administrator if you believe this is an error.'],
            ]);
        }

        $authenticated = false;

        // Check 1: Standard hashed password (password_hash column)
        if (!empty($user->password_hash) && \Illuminate\Support\Facades\Hash::check($password, $user->password_hash)) {
            $authenticated = true;
        }

        // Check 2: Legacy fallback — migrated users whose password IS their mobile number (plain text)
        if (!$authenticated && $password === $user->mobile) {
            $authenticated = true;
        }

        if ($authenticated) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect based on profile completion / approval state
            if ($user->status === 'account_approved') {
                return redirect()->intended(route('registration.wizard'));
            } elseif (in_array($user->status, ['account_pending', 'pending', 'rejected', 'blocked'])) {
                return redirect()->route('waiting.approval');
            }

            // Fully approved — go to profiles/dashboard
            return redirect()->intended(route('user.dashboard'));
        }

        throw ValidationException::withMessages([
            'login_input' => ['These credentials do not match our records.'],
        ]);
    }

    /**
     * Show Admin login form.
     */
    public function showAdminLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle Admin login request.
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => true // Only allow active admins to log in
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Logout a regular user (web guard).
     * Always redirects to the user login page.
     */
    public function logoutUser(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout(); // Clear admin session too, just in case

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Logout an admin (admin guard).
     * Always redirects to the admin login page.
     */
    public function logoutAdmin(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout(); // Clear user session too, just in case

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login-form');
    }

    /**
     * Shared logout (backward-compatible alias → user logout).
     * @deprecated Use logoutUser() or logoutAdmin() via dedicated routes.
     */
    public function logout(Request $request)
    {
        return $this->logoutUser($request);
    }
}
