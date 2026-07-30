<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use App\Models\Payment;

class ProfileWizardController extends Controller
{
    /**
     * Show the profile wizard form.
     */
    public function showWizard()
    {
        $user = Auth::user();
        $memberships = Membership::where('status', true)->get();

        return view('user.wizard', compact('user', 'memberships'));
    }

    /**
     * Save Step 1: Personal Details.
     */
    public function savePersonal(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date',
            'birth_time' => 'required|string',
            'birth_place' => 'required|string|max:255',
            'native_place' => 'required|string|max:255',
            'gotra' => 'required|string|max:100',
            'mama_gotra' => 'required|string|max:100',
            'manglik' => 'required|in:Yes,No,Anshik',
            'height' => 'required|string|max:20',
            'weight' => 'required|numeric|min:20|max:200',
            'marital_status' => 'required|string',
            'handicapped' => 'required|string',
        ]);

        $updateData = $request->only([
            'gender', 'birth_date', 'birth_time', 'birth_place',
            'native_place', 'gotra', 'mama_gotra', 'manglik',
            'height', 'marital_status', 'handicapped'
        ]);

        if ($request->has('weight')) {
            $rawWeight = $request->input('weight');
            $cleanWeight = trim(preg_replace('/(\s*kg)+/i', '', $rawWeight));
            $updateData['weight'] = !empty($cleanWeight) ? $cleanWeight . ' kg' : null;
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'weight_kg')) {
                $numericWeight = floatval(preg_replace('/[^0-9.]/', '', $cleanWeight));
                if ($numericWeight > 0) {
                    $updateData['weight_kg'] = $numericWeight;
                }
            }
        }

        Auth::user()->update($updateData);

        return response()->json(['success' => true, 'message' => 'Personal details saved.']);
    }

    /**
     * Save Step 2: Educational & Professional.
     */
    public function saveProfessional(Request $request)
    {
        $request->validate([
            'higher_education' => 'required|string|max:255',
            'occupation' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'monthly_income' => 'required|numeric|min:0',
        ]);

        Auth::user()->update($request->only([
            'higher_education', 'occupation', 'company_name', 'designation', 'monthly_income'
        ]));

        return response()->json(['success' => true, 'message' => 'Professional details saved.']);
    }

    /**
     * Save Step 3: Family.
     */
    public function saveFamily(Request $request)
    {
        $request->validate([
            'father_name' => 'required|string|max:255',
            'father_mobile' => 'nullable|string|max:20',
            'father_income' => 'nullable|numeric|min:0',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'required|string|max:255',
            'mother_mobile' => 'nullable|string|max:20',
            'mother_occupation' => 'required|string',
            'brothers' => 'required|integer|min:0',
            'brothers_married' => 'required|integer|min:0',
            'brothers_unmarried' => 'required|integer|min:0',
            'sisters' => 'required|integer|min:0',
            'sisters_married' => 'required|integer|min:0',
            'sisters_unmarried' => 'required|integer|min:0',
        ]);

        Auth::user()->update($request->only([
            'father_name', 'father_mobile', 'father_income', 'father_occupation',
            'mother_name', 'mother_mobile', 'mother_occupation',
            'brothers', 'brothers_married', 'brothers_unmarried',
            'sisters', 'sisters_married', 'sisters_unmarried'
        ]));

        return response()->json(['success' => true, 'message' => 'Family details saved.']);
    }

    /**
     * Save Step 4: Mandir Verification & References.
     */
    public function saveReferences(Request $request)
    {
        $request->validate([
            'are_you_digambar_jain' => 'required|string|in:Yes',
            'mandir' => 'required|string',
            'mandir_name' => 'required|string|max:255',
            'mandir_address' => 'required|string|max:255',
            'mandir_pincode' => 'required|string|max:20',
            'ref1_name' => 'required|string|max:255',
            'ref1_mobile' => 'required|string|max:20',
            'ref1_relation' => 'required|string|max:100',
            'ref2_name' => 'required|string|max:255',
            'ref2_mobile' => 'required|string|max:20',
            'ref2_relation' => 'required|string|max:100',
        ]);

        Auth::user()->update($request->only([
            'are_you_digambar_jain', 'mandir', 'mandir_name', 'mandir_address', 'mandir_pincode',
            'ref1_name', 'ref1_mobile', 'ref1_relation', 'ref2_name', 'ref2_mobile', 'ref2_relation'
        ]));

        return response()->json(['success' => true, 'message' => 'References and Mandir details saved.']);
    }

    /**
     * Save Step 5: Photos & Documents.
     */
    public function savePhotos(Request $request)
    {
        $request->validate([
            'profile_photo' => 'nullable|image|max:2048',
            'family_photo' => 'nullable|image|max:2048',
            'id_proof_type' => 'required|string',
            'id_proof' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $updateData = ['id_proof_type' => $request->id_proof_type];

        // Helper to convert to Base64 image
        $toBase64 = function ($file) {
            $path = $file->getRealPath();
            $type = $file->getClientMimeType();
            $data = file_get_contents($path);
            return 'data:' . $type . ';base64,' . base64_encode($data);
        };

        if ($request->hasFile('profile_photo')) {
            $updateData['profile_photo'] = $toBase64($request->file('profile_photo'));
        }

        if ($request->hasFile('family_photo')) {
            $updateData['family_photo'] = $toBase64($request->file('family_photo'));
        }

        if ($request->hasFile('id_proof')) {
            $updateData['id_proof_path'] = $toBase64($request->file('id_proof'));
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'Photos and documents updated.']);
    }

    /**
     * Save Step 6: Payment Info.
     */
    public function savePayment(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'payment_transaction_id' => 'required|string|unique:payments,transaction_id',
            'payment_screenshot' => 'required|image|max:2048',
        ]);

        $user = Auth::user();
        $membership = Membership::findOrFail($request->membership_id);

        // Convert screenshot to Base64
        $path = $request->file('payment_screenshot')->getRealPath();
        $type = $request->file('payment_screenshot')->getClientMimeType();
        $data = file_get_contents($path);
        $base64Screenshot = 'data:' . $type . ';base64,' . base64_encode($data);

        // 1. Create a payment approval record
        Payment::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'amount' => $membership->price,
            'transaction_id' => $request->payment_transaction_id,
            'payment_method' => 'UPI / Online Receipt',
            'payment_screenshot' => $base64Screenshot,
            'status' => 'pending',
            'full_name' => $user->full_name,
            'phone_number' => $user->mobile,
            'email' => $user->email,
        ]);

        // 2. Set user status to pending review
        $user->update([
            'payment_screenshot' => $base64Screenshot,
            'payment_transaction_id' => $request->payment_transaction_id,
            'payment_status' => 'pending',
            'status' => 'pending', // Pending Admin Profile Audit
        ]);

        return response()->json(['success' => true, 'message' => 'Profile setup completed! Sent to admin for review.']);
    }
}
