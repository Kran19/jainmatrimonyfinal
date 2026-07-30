<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * List all payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'membership', 'verifier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('status', 'asc') // 'pending' first
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        $memberships = Membership::where('status', true)->get();

        return view('admin.payments.index', compact('payments', 'memberships'));
    }

    /**
     * Verify payment screenshot.
     */
    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $payment) {
            $adminId = Auth::guard('admin')->id();
            
            if ($request->action === 'approve') {
                // 1. Mark payment verified
                $payment->update([
                    'status' => 'verified',
                    'verified_by' => $adminId,
                    'payment_remarks' => $request->remarks,
                ]);

                // 2. Update user payment status
                $payment->user->update([
                    'payment_status' => 'approved',
                ]);

                // 3. Create or extend user membership duration
                $membership = $payment->membership;
                if ($membership) {
                    $startDate = now();
                    $endDate = now()->addDays($membership->duration_days);

                    UserMembership::create([
                        'user_id' => $payment->user_id,
                        'membership_id' => $membership->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => 'active',
                        'can_view_contacts' => $membership->featured_profile,
                    ]);
                }
            } else {
                // Reject payment
                $payment->update([
                    'status' => 'rejected',
                    'verified_by' => $adminId,
                    'payment_remarks' => $request->remarks,
                ]);

                $payment->user->update([
                    'payment_status' => 'rejected',
                ]);
            }
        });

        return back()->with('success', "Payment transaction processed successfully.");
    }

    /**
     * Log a manual payment.
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|string|exists:users,profile_id',
            'membership_id' => 'required|exists:memberships,id',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'required|string|unique:payments,transaction_id',
            'payment_method' => 'required|string',
            'payment_remarks' => 'nullable|string',
        ]);

        $user = User::where('profile_id', $request->profile_id)->firstOrFail();
        $membership = Membership::findOrFail($request->membership_id);

        DB::transaction(function () use ($request, $user, $membership) {
            $adminId = Auth::guard('admin')->id();

            // 1. Create verified payment ledger
            Payment::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'payment_method' => $request->payment_method,
                'payment_remarks' => $request->payment_remarks,
                'status' => 'verified',
                'verified_by' => $adminId,
            ]);

            // 2. Update user status
            $user->update([
                'payment_status' => 'approved',
            ]);

            // 3. Create active membership duration
            UserMembership::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'start_date' => now(),
                'end_date' => now()->addDays($membership->duration_days),
                'status' => 'active',
                'can_view_contacts' => $membership->featured_profile,
            ]);
        });

        return back()->with('success', "Manual payment recorded and subscription activated successfully.");
    }
}
