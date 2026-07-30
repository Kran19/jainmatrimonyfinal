<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of matrimonial members.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 1. Filter by search string
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('profile_id', 'like', "%{$search}%");
            });
        }

        // 2. Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('payment_status', 'verified');
            } else {
                $query->where('status', $request->status);
            }
        }

        // 3. Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 4. Paginate
        $members = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    /**
     * Display incomplete registrations.
     */
    public function incomplete(Request $request)
    {
        $query = User::where('status', 'account_approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.members.incomplete', compact('members'));
    }

    /**
     * Display detailed profile verification info.
     */
    public function show(User $member)
    {
        // Load custom EAV data
        $customData = $member->customData()->with('field')->get();

        return view('admin.members.show', compact('member', 'customData'));
    }

    /**
     * Update candidate status (approve, reject, block).
     */
    public function updateStatus(Request $request, User $member)
    {
        $request->validate([
            'status' => 'required|in:account_pending,account_approved,pending,approved,rejected,blocked',
        ]);

        $status = $request->status;
        $updateData = ['status' => $status];

        // Generate profile_id upon Stage 2 approval if it doesn't exist
        if ($status === 'approved') {
            if (empty($member->profile_id)) {
                $updateData['profile_id'] = $this->generateProfileId();
            }
            $updateData['verified'] = true;
            $updateData['approved_by'] = Auth::guard('admin')->id();
            $updateData['approved_at'] = now();
        }

        $member->update($updateData);

        return back()->with('success', "Member status updated to " . ucfirst($status) . " successfully.");
    }

    /**
     * Remove the member from system (SoftDelete).
     */
    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->route('admin.members.index')->with('success', 'Member profile deleted successfully.');
    }

    /**
     * Thread-safe unique Profile ID generator (JDMXXXXXX).
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

    /**
     * Display account deactivation/deletion requests.
     */
    public function requests()
    {
        // Auto-sync any existing deleted or deactivated users into account_requests table
        if (\Illuminate\Support\Facades\Schema::hasTable('account_requests')) {
            $deletedOrDeactivatedUsers = User::whereIn('status', ['deleted', 'deactivated'])
                ->whereNotIn('id', function ($q) {
                    $q->select('user_id')->from('account_requests');
                })
                ->get();

            $hasCreatedAt = \Illuminate\Support\Facades\Schema::hasColumn('account_requests', 'created_at');
            $hasUpdatedAt = \Illuminate\Support\Facades\Schema::hasColumn('account_requests', 'updated_at');

            foreach ($deletedOrDeactivatedUsers as $dUser) {
                $reqType = ($dUser->status === 'deleted') ? 'deletion' : 'deactivation';
                $insertData = [
                    'user_id' => $dUser->id,
                    'request_type' => $reqType,
                    'reason' => $dUser->delete_reason ?: 'User deleted account directly from profile.',
                    'status' => ($dUser->status === 'deleted') ? 'processed' : 'pending',
                ];
                if ($hasCreatedAt) {
                    $insertData['created_at'] = $dUser->updated_at ?? now();
                }
                if ($hasUpdatedAt) {
                    $insertData['updated_at'] = now();
                }
                \DB::table('account_requests')->insert($insertData);
            }
        }

        $requests = \DB::table('account_requests')
            ->join('users', 'account_requests.user_id', '=', 'users.id')
            ->select(
                'account_requests.*',
                'users.full_name',
                'users.email',
                'users.mobile',
                'users.profile_id',
                'users.profile_photo',
                'users.gender',
                'users.status as user_status',
                'users.delete_reason'
            )
            ->orderBy('account_requests.created_at', 'desc')
            ->get();

        return view('admin.members.requests', compact('requests'));
    }

    /**
     * Process account deactivation/deletion request.
     */
    public function processRequest($id)
    {
        $req = \DB::table('account_requests')->where('id', $id)->first();
        if (!$req) {
            return back()->with('error', 'Request not found.');
        }

        $userId = $req->user_id;

        if ($req->request_type === 'deactivation') {
            User::where('id', $userId)->update([
                'status' => 'blocked',
                'is_public' => false
            ]);
        } else {
            User::where('id', $userId)->delete();
        }

        \DB::table('account_requests')
            ->where('id', $id)
            ->update(['status' => 'processed']);

        return back()->with('success', 'Request processed successfully.');
    }
}
