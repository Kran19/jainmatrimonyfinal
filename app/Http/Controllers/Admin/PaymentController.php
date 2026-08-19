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
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    /**
     * List all payments.
     * Also surfaces members who have a payment_screenshot but no payments row yet,
     * auto-creating a pending payments row for them so admin can approve/reject.
     */
    public function index(Request $request)
    {
        // Step 1: Find users who have a payment screenshot but NO existing payments row.
        $this->syncScreenshotOnlyUsers();

        // Step 2: Query the payments table normally
        $query = Payment::with(['user', 'membership', 'verifier']);

        // 1. Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Search Filter (by User name, User mobile, User email, User Profile ID, backup columns, or Transaction ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('profile_id', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Date Filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->orderByRaw("FIELD(status, 'pending', 'verified', 'rejected')")
                          ->orderBy('created_at', 'desc')
                          ->paginate(20)
                          ->withQueryString();

        $memberships = Membership::where('status', true)->get();
        $statusFilter = $request->input('status', '');

        // Counts for filter tabs (calculated without standard status limit but including search and date filters)
        $countQuery = Payment::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $countQuery->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('profile_id', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('from_date')) {
            $countQuery->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $countQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $pendingCount  = (clone $countQuery)->where('status', 'pending')->count();
        $verifiedCount = (clone $countQuery)->where('status', 'verified')->count();
        $rejectedCount = (clone $countQuery)->where('status', 'rejected')->count();

        return view('admin.payments.index', compact(
            'payments', 'memberships', 'statusFilter',
            'pendingCount', 'verifiedCount', 'rejectedCount'
        ));
    }

    /**
     * Scan for members with payment_screenshot but no payments row.
     * Auto-creates a pending payment entry so they appear for admin review.
     */
    protected function syncScreenshotOnlyUsers(): void
    {
        try {
            // Get all users who have a payment screenshot uploaded
            $usersWithScreenshot = User::whereNotNull('payment_screenshot')
                ->where('payment_screenshot', '!=', '')
                ->whereDoesntHave('payments')
                ->get();

            foreach ($usersWithScreenshot as $user) {
                Payment::create([
                    'user_id'            => $user->id,
                    'membership_id'      => null,
                    'amount'             => 0.00,
                    'transaction_id'     => 'SCR-' . $user->id . '-' . time(),
                    'payment_method'     => 'Screenshot Upload',
                    'payment_screenshot' => $user->payment_screenshot,
                    'payment_remarks'    => 'Auto-created from member screenshot upload.',
                    'status'             => $user->payment_status === 'approved' ? 'verified' : 'pending',
                    'full_name'          => $user->full_name,
                    'phone_number'       => $user->mobile,
                    'email'              => $user->email,
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail if schema mismatch — never break the payments page
        }
    }

    /**
     * Verify payment screenshot (approve or reject).
     * Uses direct DB queries to avoid any Eloquent model-level issues.
     */
    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'action'  => 'required|in:approve,reject',
            'remarks' => 'nullable|string',
        ]);

        try {
            // Get admin ID safely — fall back to null if not authenticated
            $adminId = null;
            try {
                $adminId = Auth::guard('admin')->id();
            } catch (\Exception $authEx) {
                logger()->warning('Could not get admin ID during payment verify: ' . $authEx->getMessage());
            }

            $isApprove = ($request->action === 'approve');
            $newPaymentStatus = $isApprove ? 'verified' : 'rejected';
            $newUserStatus    = $isApprove ? 'approved'  : 'rejected';

            DB::transaction(function () use ($request, $payment, $adminId, $isApprove, $newPaymentStatus, $newUserStatus) {

                // 1. Update payment row using raw DB query (bypasses Eloquent model events/guards)
                $updated = DB::table('payments')
                    ->where('id', $payment->id)
                    ->update([
                        'status'          => $newPaymentStatus,
                        'verified_by'     => $adminId,
                        'payment_remarks' => $request->remarks ?? null,
                    ]);

                logger()->info("Payment #{$payment->id} status set to {$newPaymentStatus}. Rows updated: {$updated}");

                // 2. Update linked user payment_status and paid_at
                if ($payment->user_id) {
                    $userUpdateData = ['payment_status' => $newUserStatus];
                    if ($isApprove) {
                        $userUpdateData['paid_at'] = now();
                    } else {
                        $userUpdateData['paid_at'] = null;
                    }
                    $userUpdated = DB::table('users')
                        ->where('id', $payment->user_id)
                        ->update($userUpdateData);

                    logger()->info("User #{$payment->user_id} payment_status set to {$newUserStatus}. Rows updated: {$userUpdated}");
                }

                // 3. If approving and a membership plan is linked, activate it
                if ($isApprove) {
                    $membership = $payment->membership;
                    if ($membership) {
                        // Avoid duplicate subscriptions
                        $exists = DB::table('user_memberships')
                            ->where('user_id', $payment->user_id)
                            ->where('membership_id', $membership->id)
                            ->where('status', 'active')
                            ->exists();

                        if (!$exists) {
                            UserMembership::create([
                                'user_id'           => $payment->user_id,
                                'membership_id'     => $membership->id,
                                'start_date'        => now(),
                                'end_date'          => now()->addDays($membership->duration_days),
                                'status'            => 'active',
                                'can_view_contacts' => $membership->featured_profile ?? false,
                            ]);

                            logger()->info("Membership #{$membership->id} activated for user #{$payment->user_id}");
                        }
                    }
                }
            });

            $msg = $isApprove ? 'Payment approved! Member is now marked as Paid.' : 'Payment rejected.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            logger()->error("Payment verify FAILED for payment #{$payment->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Log a manual payment.
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'profile_id'      => 'required|string|exists:users,profile_id',
            'membership_id'   => 'required|exists:memberships,id',
            'amount'          => 'required|numeric|min:0',
            'transaction_id'  => 'required|string|unique:payments,transaction_id',
            'payment_method'  => 'required|string',
            'payment_remarks' => 'nullable|string',
        ]);

        $user       = User::where('profile_id', $request->profile_id)->firstOrFail();
        $membership = Membership::findOrFail($request->membership_id);

        DB::transaction(function () use ($request, $user, $membership) {
            $adminId = Auth::guard('admin')->id();

            // 1. Create verified payment ledger
            Payment::create([
                'user_id'        => $user->id,
                'membership_id'  => $membership->id,
                'amount'         => $request->amount,
                'transaction_id' => $request->transaction_id,
                'payment_method' => $request->payment_method,
                'payment_remarks'=> $request->payment_remarks,
                'status'         => 'verified',
                'verified_by'    => $adminId,
                'full_name'      => $user->full_name,
                'phone_number'   => $user->mobile,
                'email'          => $user->email,
            ]);

            // 2. Update user payment_status and paid_at
            $user->update([
                'payment_status' => 'approved',
                'paid_at' => now(),
            ]);

            // 3. Create active membership
            UserMembership::create([
                'user_id'          => $user->id,
                'membership_id'    => $membership->id,
                'start_date'       => now(),
                'end_date'         => now()->addDays($membership->duration_days),
                'status'           => 'active',
                'can_view_contacts'=> $membership->featured_profile ?? false,
            ]);
        });

        return back()->with('success', 'Manual payment recorded and subscription activated successfully.');
    }
}
