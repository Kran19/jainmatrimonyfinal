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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|regex:/^[0-9]{10}$/|unique:users,mobile,' . $user->id,
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date',
            'birth_time' => 'required|string',
            'birth_place' => 'required|string|max:255',
            'native_place' => 'required|string|max:255',
            'cast' => 'required|string|max:100',
            'custom_cast' => 'nullable|string|max:100',
            'subcast' => 'nullable|string|max:100',
            'custom_subcast' => 'nullable|string|max:100',
            'gotra' => 'required|string|max:100',
            'mama_gotra' => 'required|string|max:100',
            'manglik' => 'required|in:Yes,No',
            'height' => 'required|string|max:20',
            'weight' => 'required|numeric|min:20|max:200',
            'marital_status' => 'required|in:Never Married,Widow,Widower,Divorce',
            'handicapped' => 'required|in:Yes,No',
            
            // Professional
            'higher_education' => 'required|string|max:255',
            'occupation' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'monthly_income' => 'required|numeric|min:0',
            
            // Family
            'father_name' => 'required|string|max:255',
            'father_mobile' => 'nullable|string|max:20',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'required|string|max:255',
            'mother_mobile' => 'nullable|string|max:20',
            'mother_occupation' => 'required|string',
            'brothers' => 'required|integer|min:0',
            'brothers_focused' => 'nullable',
            'brothers_married' => 'required|integer|min:0',
            'brothers_unmarried' => 'required|integer|min:0',
            'sisters' => 'required|integer|min:0',
            'sisters_married' => 'required|integer|min:0',
            'sisters_unmarried' => 'required|integer|min:0',

            // References
            'ref1_name' => 'required|string|max:255',
            'ref1_mobile' => 'required|string|max:20',
            'ref1_relation' => 'required|string|max:100',
            'ref2_name' => 'required|string|max:255',
            'ref2_mobile' => 'required|string|max:20',
            'ref2_relation' => 'required|string|max:100',
            
            // Files
            'profile_photo' => 'nullable|image|max:10240',
            'family_photo' => 'nullable|image|max:10240',
            'id_proof' => 'nullable|image|max:10240',
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
            'full_name', 'email', 'mobile', 'gender', 'birth_date', 'birth_time', 'birth_place',
            'native_place', 'cast', 'subcast', 'custom_subcast', 'gotra', 'mama_gotra', 'manglik', 'height', 'weight', 'marital_status',
            'handicapped', 'higher_education', 'occupation', 'company_name', 'designation', 'monthly_income',
            'father_name', 'father_mobile', 'father_occupation', 'mother_name', 'mother_mobile', 'mother_occupation',
            'brothers', 'brothers_married', 'brothers_unmarried', 'sisters', 'sisters_married', 'sisters_unmarried',
            'ref1_name', 'ref1_mobile', 'ref1_relation', 'ref2_name', 'ref2_mobile', 'ref2_relation',
            'current_address', 'permanent_address', 'pin_code', 'languages', 'hobbies', 'partner_preference'
        ]);

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

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
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

        if ($request->hasFile('id_proof')) {
            $file = $request->file('id_proof');
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
     * Delete candidate profile (Deactivate)
     */
    public function deleteProfile(Request $request)
    {
        $user = Auth::user();
        
        // Ensure delete_reason column exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'delete_reason')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('delete_reason')->nullable();
            });
        }
        
        $reason = $request->input('delete_reason');
        if (empty($reason)) {
            $reason = 'User deactivated profile directly.';
        } elseif ($reason === 'Other') {
            $reason = 'Other: ' . $request->input('delete_reason_other');
        }
        
        // Deactivate the user by setting status to deactivated
        DB::table('users')->where('id', $user->id)->update([
            'delete_reason' => $reason,
            'status' => 'deactivated',
            'is_public' => false
        ]);
        
        Auth::logout();
        
        return redirect()->route('login')->with('success', 'Your profile has been deactivated and is no longer visible.');
    }
}
