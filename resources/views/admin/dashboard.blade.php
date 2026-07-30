@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('header_title', 'Analytical Dashboard')

@section('content')
<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Members</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</h3>
        </div>
        <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
            <i class="fa-solid fa-users text-xl"></i>
        </div>
    </div>
    
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pending Profiles</p>
            <h3 class="text-2xl font-bold text-orange-600 mt-1">{{ $pendingUsers }}</h3>
        </div>
        <div class="p-2.5 bg-orange-50 text-orange-600 rounded-xl">
            <i class="fa-solid fa-user-clock text-xl"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Revenue</p>
            <h3 class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
        </div>
        <div class="p-2.5 bg-green-50 text-green-600 rounded-xl">
            <i class="fa-solid fa-wallet text-xl"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Active Paid Plans</p>
            <h3 class="text-2xl font-bold text-pink-600 mt-1">{{ $activeMemberships }}</h3>
        </div>
        <div class="p-2.5 bg-pink-50 text-pink-600 rounded-xl">
            <i class="fa-solid fa-crown text-xl"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Website Visitors</p>
            <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($visitorCount) }}</h3>
        </div>
        <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
            <i class="fa-solid fa-globe text-xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
    <!-- Gender Distribution & Stats -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Gender Ratio</h3>
        <div class="flex items-center justify-around h-48">
            <div class="text-center">
                <i class="fa-solid fa-mars text-5xl text-blue-500"></i>
                <h4 class="font-bold text-gray-900 text-xl mt-2">{{ $maleCount }}</h4>
                <p class="text-sm text-gray-400">Males</p>
            </div>
            <div class="text-center">
                <i class="fa-solid fa-venus text-5xl text-pink-500"></i>
                <h4 class="font-bold text-gray-900 text-xl mt-2">{{ $femaleCount }}</h4>
                <p class="text-sm text-gray-400">Females</p>
            </div>
        </div>
    </div>

    <!-- Recent Profiles Table -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Recent Signups</h3>
            <a href="{{ route('admin.members.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-semibold">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-semibold">
                        <th class="py-2.5">Name</th>
                        <th class="py-2.5">Email</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5">Created At</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @foreach($recentProfiles as $profile)
                    <tr class="hover:bg-slate-50 transition duration-150">
                        <td class="py-3 font-semibold text-gray-900">
                            <a href="{{ route('admin.members.show', $profile->id) }}" class="hover:text-indigo-600">
                                {{ $profile->full_name }}
                            </a>
                        </td>
                        <td class="py-3 text-gray-600">{{ $profile->email }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                @if($profile->status === 'approved') bg-green-100 text-green-800
                                @elseif($profile->status === 'pending') bg-orange-100 text-orange-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ ucfirst($profile->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-400">{{ $profile->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-800 text-lg">Recent Subscription Payments</h3>
        <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-semibold">Verify Payments</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-semibold">
                    <th class="py-2.5">Member</th>
                    <th class="py-2.5">Amount</th>
                    <th class="py-2.5">Transaction ID</th>
                    <th class="py-2.5">Method</th>
                    <th class="py-2.5">Status</th>
                    <th class="py-2.5">Date</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @foreach($recentPayments as $payment)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="py-3 font-semibold text-gray-900">
                        {{ $payment->user->full_name ?? 'Unknown Candidate' }}
                    </td>
                    <td class="py-3 font-bold text-green-700">₹{{ number_format($payment->amount, 2) }}</td>
                    <td class="py-3 text-mono text-xs text-gray-600">{{ $payment->transaction_id }}</td>
                    <td class="py-3 text-gray-500">{{ $payment->payment_method }}</td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                            @if($payment->status === 'verified') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-400">{{ $payment->created_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
