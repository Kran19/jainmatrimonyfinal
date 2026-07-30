<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display candidate dashboard overview.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Calculate profile completion percentage
        $requiredFields = [
            'gender', 'birth_date', 'birth_time', 'birth_place', 'native_place',
            'gotra', 'mama_gotra', 'manglik', 'height', 'weight', 'marital_status',
            'higher_education', 'occupation', 'monthly_income',
            'father_name', 'mother_name', 'brothers', 'sisters',
            'mandir_name', 'ref1_name', 'ref2_name',
            'profile_photo', 'id_proof_path'
        ];

        $filledCount = 0;
        foreach ($requiredFields as $field) {
            if (!empty($user->{$field})) {
                $filledCount++;
            }
        }
        $completionPercentage = round(($filledCount / count($requiredFields)) * 100);

        // 2. Fetch active membership
        $activeMembership = $user->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->with('membership')
            ->first();

        return view('user.dashboard', compact('user', 'completionPercentage', 'activeMembership'));
    }
}
