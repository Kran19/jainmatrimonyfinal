<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ProfileWizardController extends Controller
{
    /**
     * Show the profile wizard form.
     */
    public function showWizard()
    {
        $user = Auth::user();

        // Fetch visible dynamic custom fields from registration_fields where is_custom = 1 and is_visible = 1
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

        $memberships = Membership::where('status', true)->get();

        // Fetch site settings for payment QR Code
        $payment_qr_code = DB::table('site_settings')->where('setting_key', 'payment_qr_code')->value('setting_value') ?? 'assets/images/qr_code.jpg';

        return view('user.wizard', compact('user', 'memberships', 'customFieldsByGroup', 'customValues', 'payment_qr_code'));
    }

    /**
     * Helper to save custom fields.
     */
    private function saveCustomFields(Request $request, $user, array $groups)
    {
        $customFields = \App\Models\RegistrationField::where('is_custom', true)
            ->where('is_visible', true)
            ->whereIn('field_group', $groups)
            ->get();

        $uploadDir = public_path('uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($customFields as $field) {
            $value = null;

            if ($field->field_type === 'file') {
                if ($request->hasFile($field->field_key)) {
                    $file = $request->file($field->field_key);
                    $filename = time() . '_' . $field->field_key . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                    $file->move($uploadDir, $filename);
                    $value = 'uploads/' . $filename;
                }
            } else {
                $value = $request->input($field->field_key);
            }

            // Validation for required custom fields
            if ($field->is_required) {
                if ($field->field_type === 'file') {
                    $exists = DB::table('user_custom_data')
                        ->where('user_id', $user->id)
                        ->where('field_id', $field->id)
                        ->exists();
                    if (!$exists && is_null($value)) {
                        throw new \Exception("The file field {$field->field_label} is required.");
                    }
                } else {
                    if (is_null($value) || $value === '') {
                        throw new \Exception("The field {$field->field_label} is required.");
                    }
                }
            }

            if (!is_null($value)) {
                DB::table('user_custom_data')->updateOrInsert(
                    ['user_id' => $user->id, 'field_id' => $field->id],
                    ['field_value' => $value, 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Step 1: Basic Information
     */
    public function saveBasic(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'are_you_digambar_jain' => 'required|in:yes,no,Yes,No',
            'filled_by' => 'required|string',
            'gender' => 'required|in:male,female,Male,Female',
            'full_name' => 'required|string|max:255',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|email|max:255',
        ], [
            'mobile.regex' => 'Please ensure all mobile numbers are exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Duplicate email check
        $dupEmail = \App\Models\User::where('email', $request->email)->where('id', '!=', $user->id)->exists();
        if ($dupEmail) {
            return response()->json(['success' => false, 'message' => 'This email address is already registered.']);
        }

        // Duplicate mobile check
        $dupMobile = \App\Models\User::where('mobile', $request->mobile)->where('id', '!=', $user->id)->exists();
        if ($dupMobile) {
            return response()->json(['success' => false, 'message' => 'This mobile number is already registered.']);
        }

        $user->update([
            'are_you_digambar_jain' => ucfirst(strtolower($request->are_you_digambar_jain)),
            'filled_by' => $request->filled_by,
            'gender' => ucfirst(strtolower($request->gender)),
            'full_name' => $request->full_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'registration_step' => 2,
        ]);

        try {
            $this->saveCustomFields($request, $user, ['Section 1: Basic Information']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Step 2: Personal Details
     */
    public function savePersonal(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'birth_date' => 'required|date',
            'birth_time_hh' => 'required|string',
            'birth_time_mm' => 'required|string',
            'birth_time_ampm' => 'required|string',
            'birth_place' => 'required|string|max:255',
            'native' => 'required|string|max:255',
            'cast' => 'required|string|max:100',
            'custom_cast' => 'nullable|string|max:100',
            'subcast' => 'nullable|string|max:100',
            'custom_subcast' => 'nullable|string|max:100',
            'gotra' => 'required|string|max:100',
            'mama_gotra' => 'required|string|max:100',
            'manglik' => 'required|in:yes,no,Yes,No',
            'height' => 'required|string|max:50',
            'weight' => 'required|string|max:50',
            'permanent_address' => 'required|string',
            'pin_code' => 'required|regex:/^[0-9]{4,6}$/',
            'current_address' => 'required|string',
            'education' => 'required|string|max:255',
            'occupation' => 'nullable|string|max:100',
            'occupation_details' => 'nullable|string|max:255',
            'annual_income' => 'nullable|numeric|min:0',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'hobbies' => 'required|string',
            'partner_preference' => 'required|string',
            'marital_status' => 'required|in:Never Married,Widow,Divorce',
            'handicapped' => 'required|in:yes,no,Yes,No',
            'languages' => 'nullable|array',
            'other_language' => 'nullable|string|max:255',
        ], [
            'pin_code.regex' => 'Pin code must be 4 to 6 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Validate Age (must be >= 18)
        $bdate = new \DateTime($request->birth_date);
        $today = new \DateTime();
        $age = $today->diff($bdate)->y;
        if ($age < 18) {
            return response()->json(['success' => false, 'message' => 'Candidate must be at least 18 years old to register.']);
        }

        // Construct Birth Time safely (supports both TIME and VARCHAR column types)
        $rawHh = intval($request->birth_time_hh);
        $rawMm = intval($request->birth_time_mm);
        $ampm = strtoupper(trim($request->birth_time_ampm));

        $hh24 = $rawHh;
        if ($ampm === 'PM' && $hh24 < 12) {
            $hh24 += 12;
        } elseif ($ampm === 'AM' && $hh24 === 12) {
            $hh24 = 0;
        }

        $birthTime24 = sprintf('%02d:%02d:00', $hh24, $rawMm);
        $birthTime12 = sprintf('%02d:%02d %s', $rawHh, $rawMm, $ampm);

        $colType = \Illuminate\Support\Facades\DB::selectOne("
            SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'birth_time'
        ");

        $birthTime = ($colType && strtolower($colType->DATA_TYPE) === 'time') ? $birthTime24 : $birthTime12;

        // Construct Languages
        $langs = $request->languages ?? [];
        if (($key = array_search('Other', $langs)) !== false) {
            unset($langs[$key]);
            if ($request->filled('other_language')) {
                $langs[] = $request->other_language;
            }
        }
        $languagesStr = implode(',', array_filter($langs));

        $occVal = $request->occupation;
        if ($occVal === 'Other' && $request->filled('occupation_details')) {
            $occVal = $request->occupation_details;
        } elseif (empty($occVal) && $request->filled('occupation_details')) {
            $occVal = $request->occupation_details;
        }

        $updateData = [
            'birth_date' => $request->birth_date,
            'birth_time' => $birthTime,
            'birth_place' => $request->birth_place,
            'native_place' => $request->native,
            'cast' => ($request->cast === 'Other') ? $request->custom_cast : $request->cast,
            'subcast' => ($request->subcast === 'Other') ? $request->custom_subcast : $request->subcast,
            'gotra' => $request->gotra,
            'mama_gotra' => $request->mama_gotra,
            'manglik' => ucfirst(strtolower($request->manglik)),
            'height' => $request->height,
            'weight' => $request->weight,
            'permanent_address' => $request->permanent_address,
            'pin_code' => $request->pin_code,
            'current_address' => $request->current_address,
            'higher_education' => $request->education,
            'occupation' => $occVal,
            'company_name' => $request->company_name,
            'designation' => $request->designation,
            'monthly_income' => $request->annual_income ?? $request->monthly_income,
            'hobbies' => $request->hobbies,
            'partner_preference' => $request->partner_preference,
            'marital_status' => $request->marital_status,
            'handicapped' => ucfirst(strtolower($request->handicapped)),
            'languages' => $languagesStr,
            'registration_step' => 3,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'weight_kg')) {
            $numericWeight = floatval(preg_replace('/[^0-9.]/', '', $request->weight));
            if ($numericWeight > 0) {
                $updateData['weight_kg'] = $numericWeight;
            }
        }

        $user->update($updateData);

        try {
            $this->saveCustomFields($request, $user, ['Section 2: Personal Details', 'Additional Information', 'Custom Fields']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Step 3: Family Details
     */
    public function saveFamily(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'father_name' => 'required|string|max:255',
            'father_mobile' => 'nullable|regex:/^[0-9]{10}$/',
            'father_income' => 'nullable|numeric|min:0',
            'father_occupation' => 'nullable|string|max:255',
            'father_occupation_details' => 'nullable|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mother_mobile' => 'nullable|regex:/^[0-9]{10}$/',
            'mother_occupation' => 'required|string',
            'mother_occupation_details' => 'nullable|string|max:255',
            'brothers' => 'required|integer|min:0|max:5',
            'brothers_married' => 'required|integer|min:0|max:5',
            'brothers_unmarried' => 'required|integer|min:0|max:5',
            'sisters' => 'required|integer|min:0|max:5',
            'sisters_married' => 'required|integer|min:0|max:5',
            'sisters_unmarried' => 'required|integer|min:0|max:5',
        ], [
            'father_mobile.regex' => 'Please ensure all mobile numbers are exactly 10 digits.',
            'mother_mobile.regex' => 'Please ensure all mobile numbers are exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $fatherOccupation = $request->father_occupation;
        if ($fatherOccupation === 'Other' && $request->filled('father_occupation_details')) {
            $fatherOccupation = $request->father_occupation_details;
        }

        $motherOccupation = $request->mother_occupation;
        if ($motherOccupation !== 'House Wife' && $motherOccupation !== '' && $request->filled('mother_occupation_details')) {
            $motherOccupation = $request->mother_occupation_details;
        }

        $user->update([
            'father_name' => $request->father_name,
            'father_mobile' => $request->father_mobile,
            'father_income' => $request->father_income,
            'father_occupation' => $fatherOccupation,
            'mother_name' => $request->mother_name,
            'mother_mobile' => $request->mother_mobile,
            'mother_occupation' => $motherOccupation,
            'mother_occupation_details' => ($request->mother_occupation !== 'House Wife' && $request->mother_occupation !== '') ? $request->mother_occupation_details : null,
            'brothers' => $request->brothers,
            'brothers_married' => $request->brothers_married,
            'brothers_unmarried' => $request->brothers_unmarried,
            'sisters' => $request->sisters,
            'sisters_married' => $request->sisters_married,
            'sisters_unmarried' => $request->sisters_unmarried,
            'registration_step' => 4,
        ]);

        try {
            $this->saveCustomFields($request, $user, ['Section 3: Family Details']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Step 4: Temple, References, Files & Payment
     */
    public function saveFinal(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'mandir_name' => 'required|string|max:255',
            'mandir_address' => 'required|string',
            'mandir_pincode' => 'required|regex:/^[0-9]{4,6}$/',
            'ref1_name' => 'required|string|max:255',
            'ref1_mobile' => 'required|regex:/^[0-9]{10}$/',
            'ref1_relation' => 'required|string|max:255',
            'ref2_name' => 'required|string|max:255',
            'ref2_mobile' => 'required|regex:/^[0-9]{10}$/',
            'ref2_relation' => 'required|string|max:255',
            'id_proof_type' => 'required|string',
            
            'profile_photo_drive_url' => 'nullable|url',
            'payment_proof_drive_url' => 'nullable|url',
            'membership_id' => 'nullable|exists:memberships,id',
            'payment_transaction_id' => 'nullable|string',
        ], [
            'mandir_pincode.regex' => 'Temple pincode must be 4 to 6 digits.',
            'ref1_mobile.regex' => 'Please ensure Reference 1 mobile number is exactly 10 digits.',
            'ref2_mobile.regex' => 'Please ensure Reference 2 mobile number is exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Custom reference validations
        if ($request->ref1_mobile === $request->ref2_mobile) {
            return response()->json(['success' => false, 'message' => 'Reference Person 1 and Reference Person 2 must have different mobile numbers.']);
        }
        if ($request->ref1_mobile === $user->mobile || $request->ref2_mobile === $user->mobile) {
            return response()->json(['success' => false, 'message' => 'Reference mobile number cannot be the same as the candidate\'s mobile number.']);
        }
        if ($request->ref1_mobile === $user->father_mobile || $request->ref2_mobile === $user->father_mobile) {
            return response()->json(['success' => false, 'message' => 'Reference mobile number cannot be the same as the father\'s mobile number.']);
        }

        // Required file check (only if not already uploaded)
        if (empty($user->profile_photo) && !$request->hasFile('photo')) {
            return response()->json(['success' => false, 'message' => 'Candidate Photo is required.']);
        }
        if (empty($user->id_proof_path) && !$request->hasFile('id_proof_path')) {
            return response()->json(['success' => false, 'message' => 'ID Proof file is required.']);
        }

        // Max File Size Checks
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($request->hasFile('photo') && $request->file('photo')->getSize() > $maxSize) {
            return response()->json(['success' => false, 'message' => 'Candidate Photo must be less than 10MB.']);
        }
        if ($request->hasFile('family_photo') && $request->file('family_photo')->getSize() > $maxSize) {
            return response()->json(['success' => false, 'message' => 'Family Photo must be less than 10MB.']);
        }
        if ($request->hasFile('id_proof_path') && $request->file('id_proof_path')->getSize() > $maxSize) {
            return response()->json(['success' => false, 'message' => 'ID Proof file must be less than 10MB.']);
        }
        if ($request->hasFile('payment_screenshot') && $request->file('payment_screenshot')->getSize() > $maxSize) {
            return response()->json(['success' => false, 'message' => 'Payment Screenshot must be less than 10MB.']);
        }

        $uploadDir = storage_path('app/public/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $photoPath = $user->profile_photo;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_photo_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $photoPath = 'storage/uploads/' . $filename;
        }

        $familyPhotoPath = $user->family_photo;
        if ($request->hasFile('family_photo')) {
            $file = $request->file('family_photo');
            $filename = time() . '_family_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $familyPhotoPath = 'storage/uploads/' . $filename;
        }

        $idProofPath = $user->id_proof_path;
        if ($request->hasFile('id_proof_path')) {
            $file = $request->file('id_proof_path');
            $filename = time() . '_idproof_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $idProofPath = 'storage/uploads/' . $filename;
        }

        $paymentScreenshotPath = $user->payment_screenshot;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $filename = time() . '_payment_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $paymentScreenshotPath = 'storage/uploads/' . $filename;
        }

        $isEdit = ($user->status === 'approved');
        $newStatus = $isEdit ? 'approved' : 'pending';

        // Save payment if details provided
        if ($request->filled('payment_transaction_id') && $request->filled('membership_id')) {
            // Uniqueness check for payment transaction ID
            $dupTx = Payment::where('transaction_id', $request->payment_transaction_id)->exists();
            if ($dupTx) {
                return response()->json(['success' => false, 'message' => 'This transaction ID has already been submitted.']);
            }

            $membership = Membership::find($request->membership_id);
            if ($membership) {
                Payment::create([
                    'user_id' => $user->id,
                    'membership_id' => $membership->id,
                    'amount' => $membership->price,
                    'transaction_id' => $request->payment_transaction_id,
                    'payment_method' => 'UPI / Online Receipt',
                    'payment_screenshot' => $paymentScreenshotPath,
                    'status' => 'pending',
                    'full_name' => $user->full_name,
                    'phone_number' => $user->mobile,
                    'email' => $user->email,
                ]);
            }
        }

        $user->update([
            'mandir_name' => $request->mandir_name,
            'mandir_address' => $request->mandir_address,
            'mandir_pincode' => $request->mandir_pincode,
            'ref1_name' => $request->ref1_name,
            'ref1_mobile' => $request->ref1_mobile,
            'ref1_relation' => $request->ref1_relation,
            'ref2_name' => $request->ref2_name,
            'ref2_mobile' => $request->ref2_mobile,
            'ref2_relation' => $request->ref2_relation,
            'profile_photo' => $photoPath,
            'family_photo' => $familyPhotoPath,
            'id_proof_type' => $request->id_proof_type,
            'id_proof_path' => $idProofPath,
            'profile_photo_drive_url' => $request->profile_photo_drive_url,
            'payment_screenshot' => $paymentScreenshotPath,
            'payment_proof_drive_url' => $request->payment_proof_drive_url,
            'payment_transaction_id' => $request->payment_transaction_id,
            'status' => $newStatus,
            'registration_step' => 4,
        ]);

        try {
            $this->saveCustomFields($request, $user, ['Section 4: Mandir Verification Details', 'Photos', 'Documents & Payment']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        // Trigger emails
        try {
            $recipient = $user->email;
            if ($recipient) {
                if ($isEdit) {
                    $subject = "Profile Updated";
                    $body = "<h2>Hello " . htmlspecialchars($user->full_name) . "</h2><p>Your profile has been successfully updated.</p>";
                    $adminSubject = "Profile Updated";
                    $adminBody = "<h2>Profile Updated</h2><p>The profile for <b>" . htmlspecialchars($user->full_name) . "</b> has been updated.</p>";
                } else {
                    $subject = "Profile Submitted for Approval";
                    $body = "<h2>Hello " . htmlspecialchars($user->full_name) . "</h2><p>Your profile has been successfully submitted and is currently pending approval by the admin. We will notify you once it is approved.</p>";
                    $adminSubject = "New Profile Registration";
                    $adminBody = "<h2>New Profile Submitted</h2><p>A new profile for <b>" . htmlspecialchars($user->full_name) . "</b> has been submitted and is pending approval.</p>";
                }

                // Send to User
                Mail::html($body, function ($message) use ($recipient, $subject) {
                    $message->to($recipient)->subject($subject);
                });

                // Send to Admin
                Mail::html($adminBody, function ($message) use ($adminSubject) {
                    $message->to('help@digambarjainparichay.com')->subject($adminSubject);
                });
            }
        } catch (\Exception $e) {
            logger()->error("Mail failed during wizard completion: " . $e->getMessage());
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'is_edit' => $isEdit]);
        }
        
        if ($isEdit) {
            return redirect()->route('profile.my')->with('success', 'Profile updated successfully.');
        } else {
            return redirect()->route('waiting.approval')->with('success', 'Profile submitted successfully. Please wait for admin approval.');
        }
    }

    /**
     * Check if mobile is already registered.
     */
    public function checkMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $mobile = $request->mobile;
        $cleanMobile = preg_replace('/^\+?91/', '', $mobile);

        $exists = \App\Models\User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($mobile, $cleanMobile) {
                $query->where('mobile', $mobile)
                    ->orWhere('mobile', $cleanMobile)
                    ->orWhere('mobile', '+91' . $cleanMobile);
            })
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'duplicate']);
        }

        return response()->json(['status' => 'ok']);
    }
}
