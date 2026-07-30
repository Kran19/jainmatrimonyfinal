<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AccountApprovalController extends Controller
{
    /**
     * Display a listing of pending Stage 1 account requests.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('status', 'account_pending');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.approvals.account-approvals', compact('members', 'search'));
    }

    /**
     * Approve Stage 1 account request.
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'account_approved']);

        // Send approval notification email
        if ($user->email) {
            try {
                $loginUrl = route('login');
                $html = "
                <div style='font-family:Arial,sans-serif;max-width:500px;margin:auto;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden'>
                  <div style='background:#7c3aed;padding:20px;text-align:center'>
                    <h2 style='color:#fff;margin:0'>Digambar Jain Matrimony</h2>
                  </div>
                  <div style='padding:30px'>
                    <h3 style='color:#16a34a;margin:0 0 12px'>🎉 Congratulations, " . htmlspecialchars($user->full_name) . "!</h3>
                    <p style='font-size:15px;color:#374151;'>Your account registration request has been <strong>successfully approved</strong> by the admin.</p>
                    <p style='font-size:14px;color:#6b7280;'>You can now log in and complete your matrimonial profile setup.</p>
                    <div style='text-align:center;margin:24px 0'>
                      <a href='{$loginUrl}' style='background:#7c3aed;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:15px;'>Login Now</a>
                    </div>
                    <p style='font-size:12px;color:#9ca3af;text-align:center;margin-top:20px;'>Digambar Jain Matrimony &mdash; Trusted Community Platform</p>
                  </div>
                </div>";

                Mail::html($html, function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Account Approved - Digambar Jain Matrimony');
                });
            } catch (\Exception $e) {
                logger()->error("Failed sending Stage 1 approval email to {$user->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Account request for {$user->full_name} has been approved.");
    }

    /**
     * Reject and delete account request.
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', "Account request for {$user->full_name} has been rejected and deleted.");
    }
}
