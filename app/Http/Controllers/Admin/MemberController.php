<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationField;
use App\Models\UserStatusLog;
use App\Notifications\ProfileApprovedNotification;
use App\Notifications\ProfileRejectedNotification;
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
            $search = trim($request->search);
            $numericSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('profile_id', 'like', "%{$search}%");

                if (!empty($numericSearch)) {
                    $q->orWhere('profile_id', 'like', "%{$numericSearch}%")
                      ->orWhere('users.id', '=', $numericSearch);
                }
            });
        }

        // 2. Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                // users.payment_status values: 'pending', 'approved', 'rejected'
                $query->where('payment_status', 'approved');
            } else {
                $query->where('status', $request->status);
            }
        }

        // 3. Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 4. Sort newest first, then paginate
        $members = $query->orderBy('users.created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    /**
     * Export members data to Excel (.csv / .xls) or PDF.
     */
    public function export(Request $request)
    {
        $query = User::with(['memberships', 'payments']);

        // 1. Filter by search string
        if ($request->filled('search')) {
            $search = trim($request->search);
            $numericSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('profile_id', 'like', "%{$search}%");

                if (!empty($numericSearch)) {
                    $q->orWhere('profile_id', 'like', "%{$numericSearch}%")
                      ->orWhere('users.id', '=', $numericSearch);
                }
            });
        }

        // 2. Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('payment_status', 'approved');
            } else {
                $query->where('status', $request->status);
            }
        }

        // 3. Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 4. Filter by date range if provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $members = $query->orderBy('users.created_at', 'desc')->get();
        $format = strtolower($request->input('format', 'excel'));

        // Handle PDF / Printable Tabular format
        if ($format === 'pdf') {
            return view('admin.members.export-pdf', compact('members'));
        }

        // Handle Excel / CSV format
        $filename = 'jain_matrimony_members_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'Member ID',
            'Profile ID',
            'Full Name',
            'Gender',
            'Status',
            'Verified',
            'Registration Date',
            'Date of Birth',
            'Age',
            'Time of Birth',
            'Place of Birth',
            'Native Place',
            'Gotra',
            'Mama Gotra',
            'Manglik',
            'Height',
            'Weight (kg)',
            'Marital Status',
            'Handicapped',
            'Higher Education',
            'Occupation',
            'Designation',
            'Company Name',
            'Annual Salary / Income (INR)',
            'Primary Mobile',
            'Email Address',
            'Current Address',
            'Permanent Address',
            'Pin Code',
            'Father Name',
            'Father Mobile',
            'Father Occupation',
            'Father Annual Income (INR)',
            'Mother Name',
            'Mother Mobile',
            'Mother Occupation',
            'Brothers (Total)',
            'Brothers Married',
            'Brothers Unmarried',
            'Sisters (Total)',
            'Sisters Married',
            'Sisters Unmarried',
            'Mandir Name',
            'Mandir Address',
            'Mandir Pincode',
            'Reference 1 Name',
            'Reference 1 Mobile',
            'Reference 1 Relation',
            'Reference 2 Name',
            'Reference 2 Mobile',
            'Reference 2 Relation',
            'Form Filled By',
            'Payment Status',
            'Payment Transaction ID',
            'Active Plan Name',
            'Amount Paid (INR)',
            'Approved At'
        ];

        $callback = function () use ($members, $columns) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens Hindi & special characters flawlessly
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns);

            foreach ($members as $m) {
                // Calculate age
                $age = 'N/A';
                if (!empty($m->birth_date)) {
                    try {
                        $dob = new \DateTime($m->birth_date);
                        $today = new \DateTime('today');
                        $age = $dob->diff($today)->y;
                    } catch (\Exception $e) {}
                }

                // Latest payment / membership
                $lastPayment = $m->payments ? $m->payments->sortByDesc('created_at')->first() : null;
                $activePlan = $m->memberships ? $m->memberships->sortByDesc('pivot.created_at')->first() : null;
                $planName = $activePlan ? $activePlan->plan_name : ($lastPayment && $lastPayment->membership ? $lastPayment->membership->plan_name : 'None');
                $planAmount = $lastPayment ? number_format($lastPayment->amount, 2, '.', '') : '0.00';

                fputcsv($handle, [
                    $m->id,
                    $m->profile_id ?? 'N/A',
                    $m->full_name ?? '',
                    $m->gender ?? '',
                    ucfirst(str_replace('_', ' ', $m->status ?? '')),
                    $m->verified ? 'Yes' : 'No',
                    $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : '',
                    $m->birth_date ?? '',
                    $age,
                    $m->birth_time ?? '',
                    $m->birth_place ?? '',
                    $m->native_place ?? '',
                    $m->gotra ?? '',
                    $m->mama_gotra ?? '',
                    $m->manglik ?? '',
                    $m->height ?? '',
                    $m->weight ?? '',
                    $m->marital_status ?? '',
                    $m->handicapped ?? '',
                    $m->higher_education ?? '',
                    $m->occupation ?? '',
                    $m->designation ?? '',
                    $m->company_name ?? '',
                    $m->monthly_income ? number_format($m->monthly_income, 2, '.', '') : '',
                    $m->mobile ? "\t" . $m->mobile : '',
                    $m->email ?? '',
                    $m->current_address ?? '',
                    $m->permanent_address ?? '',
                    $m->pin_code ? "\t" . $m->pin_code : '',
                    $m->father_name ?? '',
                    $m->father_mobile ? "\t" . $m->father_mobile : '',
                    $m->father_occupation ?? '',
                    $m->father_income ? number_format($m->father_income, 2, '.', '') : '',
                    $m->mother_name ?? '',
                    $m->mother_mobile ? "\t" . $m->mother_mobile : '',
                    $m->mother_occupation ?? '',
                    $m->brothers ?? 0,
                    $m->brothers_married ?? 0,
                    $m->brothers_unmarried ?? 0,
                    $m->sisters ?? 0,
                    $m->sisters_married ?? 0,
                    $m->sisters_unmarried ?? 0,
                    $m->mandir_name ?? $m->mandir ?? '',
                    $m->mandir_address ?? '',
                    $m->mandir_pincode ? "\t" . $m->mandir_pincode : '',
                    $m->ref1_name ?? '',
                    $m->ref1_mobile ? "\t" . $m->ref1_mobile : '',
                    $m->ref1_relation ?? '',
                    $m->ref2_name ?? '',
                    $m->ref2_mobile ? "\t" . $m->ref2_mobile : '',
                    $m->ref2_relation ?? '',
                    $m->filled_by ?? '',
                    ucfirst($m->payment_status ?? 'Pending'),
                    $m->payment_transaction_id ?? ($lastPayment->transaction_id ?? ''),
                    $planName,
                    $planAmount,
                    $m->approved_at ? \Carbon\Carbon::parse($m->approved_at)->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display incomplete registrations.
     */
    public function incomplete(Request $request)
    {
        $query = User::where('status', 'account_approved');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $numericSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");

                if (!empty($numericSearch)) {
                    $q->orWhere('id', '=', $numericSearch);
                }
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
     * Show form to edit member profile (Admin Access).
     */
    public function edit(User $member)
    {
        $customData = $member->customData()->with('field')->get();
        return view('admin.members.edit', compact('member', 'customData'));
    }

    /**
     * Update member profile details by Admin.
     */
    public function update(Request $request, User $member)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id,
            'mobile' => 'required|string|digits:10|unique:users,mobile,' . $member->id,
            'gender' => 'nullable|string|in:Male,Female',
            'status' => 'required|string|in:account_pending,account_approved,pending,approved,rejected,blocked,deleted',
            'income_type' => 'nullable|string|in:Monthly,Yearly',
            'profile_photo_file' => 'nullable|image|max:10240',
            'horoscope_photo_file' => 'nullable|image|max:10240',
            'id_proof_photo_file' => 'nullable|image|max:10240',
        ], [
            'mobile.digits' => 'Mobile number must be exactly 10 numeric digits.',
        ]);

        $input = $request->except(['_token', '_method', 'profile_photo_file', 'horoscope_photo_file', 'id_proof_photo_file']);

        // Sanitize mobile number to 10 numeric digits
        if (isset($input['mobile'])) {
            $input['mobile'] = preg_replace('/[^0-9]/', '', (string)$input['mobile']);
        }

        // 1. Sanitize numeric income fields
        if (isset($input['monthly_income'])) {
            $incomeVal = preg_replace('/[^0-9.]/', '', (string)$input['monthly_income']);
            $input['monthly_income'] = ($incomeVal !== '') ? (float)$incomeVal : null;
        }
        if (isset($input['father_income'])) {
            $fIncomeVal = preg_replace('/[^0-9.]/', '', (string)$input['father_income']);
            $input['father_income'] = ($fIncomeVal !== '') ? (float)$fIncomeVal : null;
        }

        // 2. Parse 24-hour SQL birth_time if provided
        if (!empty($input['birth_time'])) {
            $input['birth_time'] = parse_birth_time_for_db($input['birth_time']);
        }

        // 3. Status changes & Profile ID generation if approved
        if ($input['status'] === 'approved') {
            if (empty($member->profile_id) && empty($input['profile_id'])) {
                $input['profile_id'] = $this->generateProfileId();
            }
            $input['verified'] = true;
            $input['approved_by'] = Auth::guard('admin')->id();
            $input['approved_at'] = now();
            if ($member->status !== 'approved' || empty($member->approval_date)) {
                $input['approval_date'] = now()->toDateString();
                $input['expiry_date'] = now()->addMonths(12)->toDateString();
            }
            $input['blocked_at'] = null;
            $input['rejected_at'] = null;
            $input['rejected_by'] = null;
            $input['rejection_reason'] = null;
            $input['is_public'] = true;
        } elseif ($input['status'] === 'blocked') {
            $input['blocked_at'] = now();
            $input['approved_at'] = null;
            $input['approval_date'] = null;
            $input['rejected_at'] = null;
            $input['rejected_by'] = null;
            $input['rejection_reason'] = null;
            $input['is_public'] = false;
        } elseif ($input['status'] === 'rejected') {
            $input['rejected_at'] = now();
            $input['rejected_by'] = Auth::guard('admin')->id();
            $input['approved_at'] = null;
            $input['approval_date'] = null;
            $input['blocked_at'] = null;
            $input['is_public'] = false;
        } else {
            $input['approved_at'] = null;
            $input['approval_date'] = null;
            $input['blocked_at'] = null;
            $input['rejected_at'] = null;
            $input['rejected_by'] = null;
            $input['rejection_reason'] = null;
            if (in_array($input['status'], ['deleted'])) {
                $input['is_public'] = false;
            }
        }

        // 4. Handle file uploads
        $uploadDir = storage_path('app/public/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('profile_photo_file') && $request->file('profile_photo_file')->isValid()) {
            $file = $request->file('profile_photo_file');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'user_' . $member->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_photo.' . $ext;
            $file->move($uploadDir, $filename);
            $input['profile_photo'] = 'storage/uploads/' . $filename;
        }
        if ($request->hasFile('horoscope_photo_file') && $request->file('horoscope_photo_file')->isValid()) {
            $file = $request->file('horoscope_photo_file');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'user_' . $member->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_horoscope.' . $ext;
            $file->move($uploadDir, $filename);
            $input['horoscope_photo'] = 'storage/uploads/' . $filename;
        }
        if ($request->hasFile('id_proof_photo_file') && $request->file('id_proof_photo_file')->isValid()) {
            $file = $request->file('id_proof_photo_file');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'user_' . $member->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_idproof.' . $ext;
            $file->move($uploadDir, $filename);
            $input['id_proof_photo'] = 'storage/uploads/' . $filename;
        }

        // 5. Save Custom EAV Data if submitted
        if ($request->has('custom_data') && is_array($request->custom_data)) {
            foreach ($request->custom_data as $fieldId => $val) {
                \App\Models\UserCustomData::updateOrCreate(
                    ['user_id' => $member->id, 'field_id' => $fieldId],
                    ['field_value' => is_array($val) ? implode(', ', $val) : (string)$val]
                );
            }
        }

        // 6. Update Eloquent model
        $member->update($input);

        return redirect()->route('admin.members.show', $member->id)->with('success', 'Candidate profile updated successfully by Admin.');
    }

    /**
     * Update candidate status (approve, reject, block).
     */
    public function updateStatus(Request $request, User $member)
    {
        $request->validate([
            'status' => 'required|in:account_pending,account_approved,pending,approved,rejected,blocked',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
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
            if ($member->status !== 'approved' || empty($member->approval_date)) {
                $updateData['approval_date'] = now()->toDateString();
                $updateData['expiry_date'] = now()->addMonths(12)->toDateString();
            }
            // Clear rejection and block details
            $updateData['rejection_reason'] = null;
            $updateData['rejected_at'] = null;
            $updateData['rejected_by'] = null;
            $updateData['blocked_at'] = null;

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
                $updateData['is_approved'] = true;
            }
        } elseif ($status === 'rejected') {
            $updateData['rejected_at'] = now();
            $updateData['rejected_by'] = Auth::guard('admin')->id();
            $updateData['rejection_reason'] = $request->rejection_reason;
            $updateData['approved_at'] = null;
            $updateData['approval_date'] = null;
            $updateData['approved_by'] = null;
            $updateData['blocked_at'] = null;

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
                $updateData['is_approved'] = false;
            }
        } elseif ($status === 'blocked') {
            $updateData['blocked_at'] = now();
            $updateData['approved_at'] = null;
            $updateData['approval_date'] = null;
            $updateData['approved_by'] = null;
            $updateData['rejected_at'] = null;
            $updateData['rejected_by'] = null;
            $updateData['rejection_reason'] = null;

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
                $updateData['is_approved'] = false;
            }
        } else {
            $updateData['approved_at'] = null;
            $updateData['approval_date'] = null;
            $updateData['approved_by'] = null;
            $updateData['rejected_at'] = null;
            $updateData['rejected_by'] = null;
            $updateData['rejection_reason'] = null;
            $updateData['blocked_at'] = null;

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
                $updateData['is_approved'] = false;
            }
        }

        $member->update($updateData);

        // Log status change
        try {
            UserStatusLog::create([
                'user_id' => $member->id,
                'status' => $status,
                'reason' => $status === 'rejected' ? $request->rejection_reason : ($status === 'approved' ? 'Profile approved by admin.' : 'Status modified by admin.'),
                'performed_by' => Auth::guard('admin')->id(),
                'performed_by_type' => 'admin',
            ]);
        } catch (\Exception $e) {
            logger()->error("Failed to log status change: " . $e->getMessage());
        }

        // Notify user
        try {
            if ($status === 'approved') {
                $member->notify(new ProfileApprovedNotification());
            } elseif ($status === 'rejected') {
                $member->notify(new ProfileRejectedNotification($request->rejection_reason));
            }
        } catch (\Exception $e) {
            logger()->error("Failed to notify user of status update: " . $e->getMessage());
        }

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
            ->leftJoin('users', 'account_requests.user_id', '=', 'users.id')
            ->select(
                'account_requests.*',
                'users.full_name',
                'users.email',
                'users.mobile',
                'users.profile_id',
                'users.profile_photo',
                'users.gender',
                'users.status as user_status'
            )
            ->orderByRaw("CASE WHEN account_requests.status = 'pending' THEN 0 ELSE 1 END ASC")
            ->orderBy('account_requests.created_at', 'desc')
            ->get();

        return view('admin.members.requests', compact('requests'));
    }

    /**
     * Approve account deactivation/deletion request by Admin.
     */
    public function approveRequest($id)
    {
        $req = \DB::table('account_requests')->where('id', $id)->first();
        if (!$req) {
            return back()->with('error', 'Request not found.');
        }

        $userId = $req->user_id;
        $user = User::withTrashed()->find($userId);

        if ($user) {
            if ($req->request_type === 'deactivation') {
                $user->update([
                    'status' => 'blocked',
                    'is_public' => false,
                    'blocked_at' => now(),
                ]);

                try {
                    \App\Models\UserStatusLog::create([
                        'user_id' => $user->id,
                        'status' => 'blocked',
                        'reason' => 'Admin approved account deactivation request. User reason: ' . $req->reason,
                        'performed_by' => Auth::guard('admin')->id(),
                        'performed_by_type' => 'admin',
                    ]);
                } catch (\Exception $e) {
                    logger()->error("Failed to log status change on deactivation approval: " . $e->getMessage());
                }
            } else {
                // Deletion: permanently delete member from public visibility & mark deleted
                $now = now();
                $updateData = [
                    'status' => 'deleted',
                    'is_public' => false,
                    'deleted_at' => $now,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'delete_reason')) {
                    $updateData['delete_reason'] = $req->reason;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'deletion_count')) {
                    $updateData['deletion_count'] = intval($user->deletion_count ?? 0) + 1;
                }

                \DB::table('users')->where('id', $userId)->update($updateData);

                try {
                    \App\Models\UserStatusLog::create([
                        'user_id' => $user->id,
                        'status' => 'deleted',
                        'reason' => 'Admin approved account deletion request. User reason: ' . $req->reason,
                        'performed_by' => Auth::guard('admin')->id(),
                        'performed_by_type' => 'admin',
                    ]);
                } catch (\Exception $e) {
                    logger()->error("Failed to log status change on deletion approval: " . $e->getMessage());
                }
            }
        }

        \DB::table('account_requests')
            ->where('id', $id)
            ->update([
                'status' => 'processed',
                'updated_at' => now(),
            ]);

        $actionType = ($req->request_type === 'deactivation') ? 'deactivated' : 'permanently deleted';
        return back()->with('success', "Member account {$actionType} successfully.");
    }

    /**
     * Reject account deactivation/deletion request by Admin.
     */
    public function rejectRequest($id)
    {
        $req = \DB::table('account_requests')->where('id', $id)->first();
        if (!$req) {
            return back()->with('error', 'Request not found.');
        }

        \DB::table('account_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Deactivation / deletion request rejected. Member account remains active.');
    }

    /**
     * Legacy alias for approveRequest.
     */
    public function processRequest($id)
    {
        return $this->approveRequest($id);
    }

    /**
     * Disassociate duplicate legacy photo assignments across candidate profiles.
     */
    public function fixDuplicatePhotos()
    {
        $duplicates = \DB::table('users')
            ->select('profile_photo', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('profile_photo')
            ->where('profile_photo', '!=', '')
            ->groupBy('profile_photo')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            return back()->with('success', 'All candidate profile photos are already unique. No duplicate photo assignments found.');
        }

        $clearedCount = 0;
        $names = [];

        foreach ($duplicates as $dup) {
            $photoPath = $dup->profile_photo;
            $users = User::where('profile_photo', $photoPath)->orderBy('id', 'asc')->get();

            // Keep photo for the first user, clear for subsequent duplicate users
            foreach ($users->slice(1) as $secondaryUser) {
                $secondaryUser->profile_photo = null;
                $secondaryUser->save();
                $clearedCount++;
                $names[] = "{$secondaryUser->full_name} ({$secondaryUser->profile_id})";
            }
        }

        $namesList = implode(', ', array_slice($names, 0, 5));
        if (count($names) > 5) {
            $namesList .= ' and ' . (count($names) - 5) . ' more';
        }

        return back()->with('success', "Fixed {$clearedCount} duplicate photo assignments ({$namesList}). Profiles now display their unique avatar badge.");
    }
}
