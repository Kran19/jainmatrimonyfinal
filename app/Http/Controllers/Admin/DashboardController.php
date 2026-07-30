<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\AccountRequest;
use App\Models\UserMembership;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // 1. Calculate dashboard aggregates
        $totalUsers = User::count();
        $pendingUsers = User::pending()->count();
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');
        
        $activeMemberships = UserMembership::active()->count();
        $pendingDeletionRequests = AccountRequest::where('status', 'pending')->count();

        // Fetch Visitor Count
        $visitorCount = DB::table('site_settings')->where('setting_key', 'visitor_count')->value('setting_value') ?: 0;

        // 2. Calculate Gender Distribution (Male vs Female)
        $genderDistribution = User::select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender');

        $maleCount = $genderDistribution->get('Male', 0);
        $femaleCount = $genderDistribution->get('Female', 0);

        // 3. Retrieve recent profile submissions
        $recentProfiles = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Retrieve recent payments
        $recentPayments = Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingUsers',
            'totalRevenue',
            'activeMemberships',
            'pendingDeletionRequests',
            'maleCount',
            'femaleCount',
            'recentProfiles',
            'recentPayments',
            'visitorCount'
        ));
    }
}
