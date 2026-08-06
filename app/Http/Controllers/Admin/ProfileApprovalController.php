<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Notifications\ProfileApprovedNotification;
use App\Notifications\ProfileRejectedNotification;

class ProfileApprovalController extends Controller
{
    /**
     * Display a listing of pending profile approvals.
     */
    public function index()
    {
        $pendingMembers = User::pending()->orderBy('updated_at', 'asc')->paginate(15);
        return view('admin.approvals.index', compact('pendingMembers'));
    }

    /**
     * Approve candidate profile.
     */
    public function approve(User $member)
    {
        $updateData = [
            'status' => 'approved',
            'verified' => true,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
            'approval_date' => now()->toDateString(),
            'expiry_date' => now()->addMonths(12)->toDateString(),
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
            $updateData['is_approved'] = true;
        }

        // Generate profile_id if not already set
        if (empty($member->profile_id)) {
            $updateData['profile_id'] = $this->generateProfileId();
        }

        $member->update($updateData);

        // Log status change
        try {
            UserStatusLog::create([
                'user_id' => $member->id,
                'status' => 'approved',
                'reason' => 'Profile approved by admin.',
                'performed_by' => Auth::guard('admin')->id(),
                'performed_by_type' => 'admin',
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to log status change: " . $e->getMessage());
        }

        // Notify user
        try {
            $member->notify(new ProfileApprovedNotification());
        } catch (\Exception $e) {
            logger()->error("Failed to notify user: " . $e->getMessage());
        }

        return redirect()->route('admin.approvals.index')
            ->with('success', "Profile for {$member->full_name} has been approved and activated.");
    }

    /**
     * Reject candidate profile.
     */
    public function reject(Request $request, User $member)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $updateData = [
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::guard('admin')->id(),
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
            $updateData['is_approved'] = false;
        }

        $member->update($updateData);

        // Log status change
        try {
            UserStatusLog::create([
                'user_id' => $member->id,
                'status' => 'rejected',
                'reason' => $request->rejection_reason,
                'performed_by' => Auth::guard('admin')->id(),
                'performed_by_type' => 'admin',
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to log status change: " . $e->getMessage());
        }

        // Notify user
        try {
            $member->notify(new ProfileRejectedNotification($request->rejection_reason));
        } catch (\Exception $e) {
            logger()->error("Failed to notify user of rejection: " . $e->getMessage());
        }

        return redirect()->route('admin.approvals.index')
            ->with('success', "Profile for {$member->full_name} has been rejected.");
    }

    /**
     * Helper to generate unique profile IDs.
     */
    protected function generateProfileId(): string
    {
        do {
            $randomDigits = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $profileId = 'JDM' . $randomDigits;
            $exists = User::where('profile_id', $profileId)->exists();
        } while ($exists);

        return $profileId;
    }
}

