<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Show form to change password.
     */
    public function showChangeForm()
    {
        return view('user.change-password');
    }

    /**
     * Update candidate password.
     * Matches Core PHP change-password.php behavior exactly.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'New passwords do not match.',
        ]);

        if ($request->new_password === $request->current_password) {
            return back()->withInput()->with('error', 'New password must be different from current password.');
        }

        $user = Auth::guard('web')->user();

        // Verify current password — support legacy mobile-as-password fallback
        $isValidCurrent = false;
        if (!empty($user->password_hash)) {
            $isValidCurrent = Hash::check($request->current_password, $user->password_hash);
        } else {
            // Legacy: migrated users whose password was their mobile number
            $isValidCurrent = ($request->current_password === $user->mobile);
        }

        if (!$isValidCurrent) {
            return back()->with('error', 'Incorrect current password.');
        }

        $newHash = Hash::make($request->new_password);

        // Update password_hash and mark has_set_password = true
        $user->update([
            'password_hash'    => $newHash,
            'has_set_password' => true,
        ]);

        return back()->with('success', 'Password successfully changed.');
    }
}
