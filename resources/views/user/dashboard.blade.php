@extends('layouts.app')

@section('title', 'My Dashboard - Jain Digambar Matrimony')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    
    <!-- Top Welcoming Card -->
    <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-3xl p-8 text-white shadow-lg mb-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10">
            <i class="fa-solid fa-gopuram text-[180px]"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                <span class="text-xs font-bold uppercase bg-indigo-500/40 px-3 py-1 rounded-full text-indigo-200">Candidate Dashboard</span>
                <h2 class="text-3xl font-extrabold mt-3">Welcome, {{ $user->full_name }}</h2>
                <p class="text-sm text-indigo-200 mt-1 font-mono">
                    Profile ID: {{ $user->profile_id ?? 'Not Assigned (Pending Verification)' }}
                </p>
            </div>
            @if($user->status === 'approved')
            <div class="flex-shrink-0">
                <a href="{{ route('profiles') }}" class="px-6 py-3 bg-white text-indigo-800 font-bold rounded-xl text-sm shadow-md hover:bg-slate-50 transition duration-150 flex items-center">
                    <i class="fa-solid fa-users-viewfinder mr-2"></i> Browse Candidates
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Left: Status & Actions -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Alert States based on approval state -->
            @if($user->status === 'account_approved')
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex gap-4">
                    <div class="text-amber-500 text-3xl flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900 text-base">Incomplete Profile</h4>
                        <p class="text-sm text-amber-700 mt-1 leading-relaxed">
                            Your account is active, but your matrimonial candidate profile is incomplete. Please finish the wizard to let our admins verify and list you.
                        </p>
                        <a href="{{ route('registration.wizard') }}" class="mt-4 inline-block px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-sm transition">
                            Complete My Profile <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            @elseif($user->status === 'pending')
                <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 flex gap-4">
                    <div class="text-indigo-600 text-3xl flex-shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-indigo-950 text-base">Verification in Progress</h4>
                        <p class="text-sm text-indigo-700 mt-1 leading-relaxed">
                            Thank you! Your profile has been submitted and is currently undergoing admin review. We will verify your mandir and references. You will be notified once activated.
                        </p>
                    </div>
                </div>
            @elseif($user->status === 'approved')
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex gap-4">
                    <div class="text-emerald-600 text-3xl flex-shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-950 text-base">Verified & Active Profile</h4>
                        <p class="text-sm text-emerald-700 mt-1 leading-relaxed">
                            Congratulations! Your profile has been verified and is visible to other registered Digambar Jain candidates.
                        </p>
                    </div>
                </div>
            @elseif($user->status === 'blocked')
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex gap-4">
                    <div class="text-red-600 text-3xl flex-shrink-0">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-red-950 text-base">Account Suspended</h4>
                        <p class="text-sm text-red-700 mt-1 leading-relaxed">
                            Your account profile has been suspended by administration. Please contact support.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Profile Summary Card -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="md:col-span-2 border-b pb-2 mb-2 font-bold text-gray-800">Quick Profile Details</div>
                <div><span class="text-gray-400 font-semibold">Gotra:</span> <span class="font-bold text-gray-900">{{ $user->gotra ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Mama Gotra:</span> <span class="font-bold text-gray-900">{{ $user->mama_gotra ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Native Place:</span> <span class="font-bold text-gray-900">{{ $user->native_place ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Occupation:</span> <span class="font-bold text-gray-900">{{ $user->occupation ?? 'N/A' }}</span></div>
            </div>

        </div>

        <!-- Right Sidebar stats -->
        <div class="space-y-6">
            
            <!-- Profile Progress -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                <h3 class="font-bold text-gray-800 text-sm mb-3">Profile Completeness</h3>
                <div class="relative w-28 h-28 mx-auto mb-4 flex items-center justify-center rounded-full border-8 border-slate-100" style="border-top-color: #4f46e5;">
                    <span class="text-2xl font-black text-gray-800">{{ $completionPercentage }}%</span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Filling education, photos, and verification references boosts visibility by 3x.
                </p>
            </div>

            <!-- Active Membership Info -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 text-sm border-b pb-2.5 mb-3 flex items-center">
                    <i class="fa-solid fa-crown text-indigo-500 mr-2"></i> Subscription Details
                </h3>
                
                @if($activeMembership)
                    <div class="space-y-2 text-xs">
                        <div><span class="text-gray-400 font-semibold">Plan Name:</span> <span class="font-bold text-gray-800">{{ $activeMembership->membership->plan_name }}</span></div>
                        <div><span class="text-gray-400 font-semibold">Price:</span> <span class="font-bold text-green-700">₹{{ number_format($activeMembership->membership->price, 2) }}</span></div>
                        <div><span class="text-gray-400 font-semibold">Valid Till:</span> <span class="font-bold text-gray-800">{{ $activeMembership->end_date->format('M d, Y') }}</span></div>
                        <div class="pt-2 text-[10px] text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded font-bold uppercase inline-block">Active Plan</div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-slate-400">No active premium plan</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
