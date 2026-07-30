<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\Admin;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\Notification;

class ProfileController extends Controller
{
    /**
     * View candidate's own profile.
     */
    public function myProfile()
    {
        $user = Auth::user();

        // Calculate Age
        $age = 'N/A';
        if (!empty($user->birth_date)) {
            $age = $user->birth_date->diff(now())->y;
        }

        // Fetch the latest payment status for the user's screenshot
        $latestPayment = Payment::where('user_id', $user->id)
            ->where('payment_method', 'Screenshot')
            ->orderBy('id', 'desc')
            ->first();
        $paymentStatus = $latestPayment ? $latestPayment->status : 'pending';

        return view('user.profile', compact('user', 'age', 'paymentStatus'));
    }

    /**
     * Handle payment screenshot upload post-wizard (e.g. if rejected or updating status).
     */
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'payment_transaction_id' => 'required|string',
            'payment_screenshot' => 'required|image|max:10240',
        ]);

        $user = Auth::user();
        $user_id = $user->id;

        // Check if user already has a pending screenshot and isn't rejected
        $latestPayment = Payment::where('user_id', $user_id)
            ->where('payment_method', 'Screenshot')
            ->orderBy('id', 'desc')
            ->first();
        $paymentStatus = $latestPayment ? $latestPayment->status : 'pending';

        if (!empty($user->payment_screenshot) && $paymentStatus !== 'rejected') {
            return back()->with('error', 'You have already uploaded a payment screenshot.');
        }

        // Check if transaction ID is already used
        $duplicateTxn = Payment::where('transaction_id', $request->payment_transaction_id)->first();
        if ($duplicateTxn) {
            return back()->with('error', 'This Transaction ID has already been submitted.');
        }

        // Handle file upload
        $uploadDir = storage_path('app/public/uploads/receipts');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $request->file('payment_screenshot');
        $newFileName = 'payment_' . $user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $newFileName);
        $dest = 'storage/uploads/receipts/' . $newFileName;

        // Update user
        $user->update([
            'payment_screenshot' => $dest,
            'payment_transaction_id' => $request->payment_transaction_id,
            'payment_status' => 'pending',
        ]);

        // Insert into payments
        $address = trim(!empty($user->current_address) ? $user->current_address : ($user->permanent_address ?? ''));
        Payment::create([
            'user_id' => $user_id,
            'full_name' => $user->full_name,
            'phone_number' => $user->mobile,
            'email' => $user->email,
            'address' => $address,
            'dob' => $user->birth_date ? $user->birth_date->toDateString() : null,
            'transaction_id' => $request->payment_transaction_id,
            'payment_screenshot' => $dest,
            'payment_method' => 'Screenshot',
            'status' => 'pending',
        ]);

        // Notify Admins
        try {
            $admins = Admin::where('status', true)->get();
            Notification::send($admins, new PaymentReceivedNotification($user, $request->payment_transaction_id, 0.00));
        } catch (\Exception $e) {
            logger()->error("Failed to notify admins of post-wizard payment receipt: " . $e->getMessage());
        }

        return back()->with('success', 'Payment screenshot uploaded successfully!');
    }

    /**
     * Show the profile edit form.
     */
    public function showEditForm()
    {
        $user = Auth::user();
        
        // Group visible dynamic custom fields by field_group
        $customFields = \App\Models\RegistrationField::where('is_custom', true)
            ->where('is_visible', true)
            ->orderBy('sort_order', 'asc')
            ->get();
            
        $customFieldsByGroup = [];
        foreach ($customFields as $field) {
            $group = $field->field_group ?: 'Additional Information';
            if ($group === 'Custom Fields') {
                $group = 'Additional Information';
            }
            $customFieldsByGroup[$group][] = $field;
        }

        // Fetch user's custom field values mapped by field_id
        $customValues = DB::table('user_custom_data')
            ->where('user_id', $user->id)
            ->pluck('field_value', 'field_id')
            ->toArray();

        return view('user.edit', compact('user', 'customFieldsByGroup', 'customValues'));
    }

    /**
     * Update candidate profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // 1. Validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:20|unique:users,mobile,' . $user->id,
            'gender' => 'required|in:Male,Female',
            'are_you_digambar_jain' => 'nullable|string|max:50',
            'filled_by' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'birth_time' => 'required|string|max:50',
            'birth_place' => 'required|string|max:255',
            'native_place' => 'required|string|max:255',
            'cast' => 'required|string|max:100',
            'custom_cast' => 'nullable|string|max:100',
            'subcast' => 'nullable|string|max:100',
            'custom_subcast' => 'nullable|string|max:100',
            'gotra' => 'required|string|max:100',
            'mama_gotra' => 'required|string|max:100',
            'manglik' => 'required|in:Yes,No',
            'height' => 'required|string|max:50',
            'weight' => 'nullable|string|max:50',
            'marital_status' => 'required|in:Never Married,Widow,Widower,Divorce',
            'handicapped' => 'required|in:Yes,No',
            
            // Professional
            'higher_education' => 'required|string|max:255',
            'occupation' => 'nullable|string|max:100',
            'custom_occupation' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            
            // Family
            'father_name' => 'required|string|max:255',
            'father_mobile' => 'nullable|string|max:20',
            'father_occupation' => 'nullable|string|max:100',
            'father_income' => 'nullable|numeric|min:0',
            'mother_name' => 'required|string|max:255',
            'mother_mobile' => 'nullable|string|max:20',
            'mother_occupation' => 'nullable|string|max:100',
            'brothers' => 'nullable|integer|min:0',
            'brothers_married' => 'nullable|integer|min:0',
            'brothers_unmarried' => 'nullable|integer|min:0',
            'sisters' => 'nullable|integer|min:0',
            'sisters_married' => 'nullable|integer|min:0',
            'sisters_unmarried' => 'nullable|integer|min:0',

            // Mandir & Community Verification
            'mandir_name' => 'nullable|string|max:255',
            'mandir_address' => 'nullable|string|max:255',
            'mandir_pincode' => 'nullable|string|max:20',

            // References
            'ref1_name' => 'nullable|string|max:255',
            'ref1_mobile' => 'nullable|string|max:20',
            'ref1_relation' => 'nullable|string|max:100',
            'ref2_name' => 'nullable|string|max:255',
            'ref2_mobile' => 'nullable|string|max:20',
            'ref2_relation' => 'nullable|string|max:100',

            // Preferences & Address
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'pin_code' => 'nullable|string|max:20',
            'languages' => 'nullable|string',
            'hobbies' => 'nullable|string',
            'partner_preference' => 'nullable|string',
            
            // Files & ID
            'id_proof_type' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|max:10240',
            'photo' => 'nullable|image|max:10240',
            'family_photo' => 'nullable|image|max:10240',
            'id_proof' => 'nullable|image|max:10240',
            'id_proof_path' => 'nullable|image|max:10240',
        ]);

        // 2. Validate custom fields
        $customFields = \App\Models\RegistrationField::where('is_custom', true)
            ->where('is_visible', true)
            ->get();

        foreach ($customFields as $field) {
            if ($field->field_type === 'file') {
                if ($field->is_required && !$request->hasFile($field->field_key)) {
                    $exists = DB::table('user_custom_data')
                        ->where('user_id', $user->id)
                        ->where('field_id', $field->id)
                        ->exists();
                    if (!$exists) {
                        return back()->withInput()->with('error', "The file field {$field->field_label} is required.");
                    }
                }
            } else {
                if ($field->is_required && is_null($request->input($field->field_key))) {
                    return back()->withInput()->with('error', "The field {$field->field_label} is required.");
                }
            }
        }

        // 3. Update core fields
        $userUpdate = $request->only([
            'full_name', 'email', 'mobile', 'gender', 'are_you_digambar_jain', 'filled_by',
            'birth_date', 'birth_time', 'birth_place', 'native_place', 'cast', 'subcast', 'custom_subcast',
            'gotra', 'mama_gotra', 'manglik', 'height', 'weight', 'marital_status', 'handicapped',
            'higher_education', 'occupation', 'company_name', 'designation', 'monthly_income',
            'father_name', 'father_mobile', 'father_occupation', 'father_income',
            'mother_name', 'mother_mobile', 'mother_occupation',
            'brothers', 'brothers_married', 'brothers_unmarried',
            'sisters', 'sisters_married', 'sisters_unmarried',
            'mandir_name', 'mandir_address', 'mandir_pincode',
            'ref1_name', 'ref1_mobile', 'ref1_relation',
            'ref2_name', 'ref2_mobile', 'ref2_relation',
            'current_address', 'permanent_address', 'pin_code',
            'languages', 'hobbies', 'partner_preference', 'id_proof_type'
        ]);

        if ($request->filled('custom_occupation') && ($request->occupation === 'Other' || empty($request->occupation))) {
            $userUpdate['occupation'] = $request->custom_occupation;
        }

        // Dual update for weight and weight_kg columns
        if ($request->has('weight')) {
            $rawWeight = $request->input('weight');
            $cleanWeight = trim(preg_replace('/(\s*kg)+/i', '', $rawWeight));
            $userUpdate['weight'] = !empty($cleanWeight) ? $cleanWeight . ' kg' : null;
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'weight_kg')) {
                $numericWeight = floatval(preg_replace('/[^0-9.]/', '', $cleanWeight));
                if ($numericWeight > 0) {
                    $userUpdate['weight_kg'] = $numericWeight;
                }
            }
        }

        // Handle Cast/Subcast custom inputs
        if ($request->cast === 'Other' && $request->filled('custom_cast')) {
            $userUpdate['cast'] = $request->custom_cast;
        }
        if ($request->subcast === 'Other' && $request->filled('custom_subcast')) {
            $userUpdate['subcast'] = $request->custom_subcast;
        }

        // File uploads for profile/family/id proof
        $uploadDir = storage_path('app/public/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('profile_photo') || $request->hasFile('photo')) {
            $file = $request->file('profile_photo') ?? $request->file('photo');
            $filename = time() . '_photo_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $userUpdate['profile_photo'] = 'storage/uploads/' . $filename;
        }

        if ($request->hasFile('family_photo')) {
            $file = $request->file('family_photo');
            $filename = time() . '_family_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $userUpdate['family_photo'] = 'storage/uploads/' . $filename;
        }

        if ($request->hasFile('id_proof') || $request->hasFile('id_proof_path')) {
            $file = $request->file('id_proof') ?? $request->file('id_proof_path');
            $filename = time() . '_idproof_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $userUpdate['id_proof_path'] = 'storage/uploads/' . $filename;
        }

        $user->update($userUpdate);

        // 4. Update dynamic custom fields
        foreach ($customFields as $field) {
            $value = null;

            if ($field->field_type === 'file') {
                if ($request->hasFile($field->field_key)) {
                    $file = $request->file($field->field_key);
                    $filename = time() . '_' . $field->field_key . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                    $file->move($uploadDir, $filename);
                    $value = 'storage/uploads/' . $filename;
                }
            } else {
                $value = $request->input($field->field_key);
            }

            if (!is_null($value)) {
                DB::table('user_custom_data')->updateOrInsert(
                    ['user_id' => $user->id, 'field_id' => $field->id],
                    ['field_value' => $value, 'updated_at' => now()]
                );
            }
        }

        return redirect()->route('profile.my')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete candidate profile (Account Deletion)
     */
    public function deleteProfile(Request $request)
    {
        $user = Auth::user();
        
        // 1. Ensure delete_reason column exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'delete_reason')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('delete_reason')->nullable();
            });
        }

        // 2. Ensure status column is relaxed if ENUM
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
            $colInfo = DB::selectOne("
                SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'status'
            ");
            if ($colInfo && strtolower($colInfo->DATA_TYPE) === 'enum') {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `status` VARCHAR(50) NULL DEFAULT 'account_approved'");
            }
        }
        
        $reason = $request->input('delete_reason');
        if (empty($reason)) {
            $reason = 'User deleted account directly from profile.';
        } elseif ($reason === 'Other') {
            $reason = 'Other: ' . $request->input('delete_reason_other');
        }

        $now = now();
        
        // 3. Mark user account as DELETED and inactive
        $updateData = [
            'delete_reason' => $reason,
            'status' => 'deleted',
            'is_public' => false
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'deleted_at')) {
            $updateData['deleted_at'] = $now;
        }

        DB::table('users')->where('id', $user->id)->update($updateData);

        // 4. Log deletion record in account_requests table for Administrative Tracking
        if (\Illuminate\Support\Facades\Schema::hasTable('account_requests')) {
            $insertData = [
                'user_id' => $user->id,
                'request_type' => 'deletion',
                'reason' => $reason,
                'status' => 'processed',
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('account_requests', 'created_at')) {
                $insertData['created_at'] = $now;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('account_requests', 'updated_at')) {
                $insertData['updated_at'] = $now;
            }
            DB::table('account_requests')->insert($insertData);
        }
        
        // 5. Logout & invalidate session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // 6. Display exact required message
        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    }
}
