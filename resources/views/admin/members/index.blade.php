@extends('layouts.admin')

@section('title', 'Manage Members - Admin Panel')
@section('header_title', 'Matrimonial Members Management')

@section('content')
<!-- Filter Panel -->
<div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 sm:mb-8">
    <form action="{{ route('admin.members.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Profile</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, Mobile, ID..."
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="account_pending" {{ request('status') === 'account_pending' ? 'selected' : '' }}>Account Pending</option>
                <option value="account_approved" {{ request('status') === 'account_approved' ? 'selected' : '' }}>Account Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Active)</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Account Deleted (by User)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter Gender</label>
            <select name="gender" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Genders</option>
                <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition duration-150 flex-grow">
                Apply Filters
            </button>
            <a href="{{ route('admin.members.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg text-sm transition duration-150 text-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Action / Export Bar -->
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-gray-100 shadow-sm">
    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-600 font-medium">
        <span class="font-bold text-slate-900 text-sm sm:text-base">{{ $members->total() }}</span> members found
        @if(request('status') || request('gender') || request('search'))
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[11px] font-semibold border border-amber-200">
                <i class="fa-solid fa-filter text-[10px]"></i> Filtered Results
            </span>
        @endif
    </div>

    <!-- Export Buttons Group -->
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <span class="text-xs font-bold text-slate-400 uppercase mr-1 hidden md:inline">Export:</span>
        <a href="{{ route('admin.members.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition duration-150" 
           title="Download full Excel sheet (.csv)">
            <i class="fa-solid fa-file-excel text-sm"></i> Export Excel
        </a>
        <a href="{{ route('admin.members.export', array_merge(request()->all(), ['format' => 'csv'])) }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition duration-150" 
           title="Download raw CSV file">
            <i class="fa-solid fa-file-csv text-sm"></i> Export CSV
        </a>
        <a href="{{ route('admin.members.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
           target="_blank"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition duration-150" 
           title="View and Print / Save PDF report">
            <i class="fa-solid fa-file-pdf text-sm"></i> Export PDF
        </a>

        <!-- Fix Duplicate Photos Button -->
        <form action="{{ route('admin.members.fix-duplicate-photos') }}" method="POST" class="inline" onsubmit="return confirm('This will scan all candidates and disassociate any shared legacy photo paths so each candidate has a unique photo/avatar. Proceed?');">
            @csrf
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 text-xs font-bold shadow-xs transition duration-150"
                    title="Disassociate duplicate legacy photos shared by members with identical names">
                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i> Fix Duplicate Photos
            </button>
        </form>
    </div>
</div>

<!-- Members Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    @if(request('status') === 'paid')
                        <th class="py-3 px-6">Member</th>
                        <th class="py-3 px-6">Plan / Amount</th>
                        <th class="py-3 px-6">Transaction Details</th>
                        <th class="py-3 px-6">Screenshot</th>
                        <th class="py-3 px-6">Paid Date</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    @else
                        <th class="py-3 px-6">Profile</th>
                        <th class="py-3 px-6">Contact Info</th>
                        <th class="py-3 px-6 text-center">Reg. Count</th>
                        <th class="py-3 px-6 text-center">Delete Count</th>
                        <th class="py-3 px-6">Registered On</th>
                        @if(request('status') === 'approved')
                            <th class="py-3 px-6">Approved Date</th>
                        @elseif(request('status') === 'blocked')
                            <th class="py-3 px-6">Blocked Date</th>
                        @elseif(request('status') === 'rejected')
                            <th class="py-3 px-6">Rejected Date</th>
                        @else
                            <th class="py-3 px-6">Status Date</th>
                        @endif
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($members as $member)
                <tr class="hover:bg-slate-50 transition duration-150">
                    @if(request('status') === 'paid')
                        {{-- Paid Member Layout matches payments ledger --}}
                        <td class="py-4 px-6 flex items-center gap-3">
                            <div class="w-10 h-10 flex-shrink-0 min-w-[40px] aspect-square rounded-full bg-slate-200 overflow-hidden border flex items-center justify-center">
                                <img src="{{ $member->profile_photo_url }}" alt="Photo" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="hover:text-indigo-600 transition">
                                        {{ $member->full_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-400 mt-1 font-mono">
                                    {{ $member->profile_id ?? 'No Profile ID' }} ({{ $member->gender ?? 'N/A' }})
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $lastPayment = $member->payments()->latest()->first();
                            @endphp
                            @if($lastPayment && $lastPayment->membership)
                                <div class="font-bold text-slate-900">{{ $lastPayment->membership->plan_name }}</div>
                                <div class="font-bold text-green-700 mt-0.5">₹{{ number_format($lastPayment->amount, 2) }}</div>
                            @else
                                <div class="font-semibold text-indigo-700">Screenshot Upload</div>
                                <div class="text-xs text-slate-400 mt-0.5 italic">No plan linked</div>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($lastPayment)
                                <div class="text-xs text-gray-700 font-medium">{{ $lastPayment->payment_method ?? 'N/A' }}</div>
                                @if($lastPayment->transaction_id && !str_starts_with($lastPayment->transaction_id, 'SCR-'))
                                    <div class="font-mono text-xs text-slate-400 mt-0.5">{{ $lastPayment->transaction_id }}</div>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">Direct Activation</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $screenshotPath = $lastPayment->payment_screenshot ?? $member->payment_screenshot;
                            @endphp
                            @if($screenshotPath)
                                <a href="/image?file={{ urlencode($screenshotPath) }}" target="_blank">
                                    <img src="/image?file={{ urlencode($screenshotPath) }}" alt="Receipt" class="w-10 h-10 object-cover rounded border shadow-sm hover:opacity-85 transition">
                                </a>
                            @else
                                <span class="text-xs text-slate-400">None</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-medium text-gray-700">
                            @php
                                $paidDate = $member->paid_at 
                                    ? \Carbon\Carbon::parse($member->paid_at) 
                                    : ($member->payments()->where('status', 'verified')->latest()->first()?->created_at 
                                        ?? $member->memberships()->latest()->first()?->pivot?->start_date);
                            @endphp
                            {{ $paidDate ? \Carbon\Carbon::parse($paidDate)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                Approved
                            </span>
                        </td>
                    @else
                        {{-- Standard Member Layout --}}
                        <td class="py-4 px-6 flex items-center gap-3">
                            <div class="w-10 h-10 flex-shrink-0 min-w-[40px] aspect-square rounded-full bg-slate-200 overflow-hidden border flex items-center justify-center">
                                <img src="{{ $member->profile_photo_url }}" alt="Photo" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="hover:text-indigo-600 transition">
                                        {{ $member->full_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-400 mt-1 font-mono">
                                    {{ $member->profile_id ?? 'No Profile ID' }} ({{ $member->gender ?? 'N/A' }})
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-gray-900 font-semibold">{{ $member->mobile }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $member->email }}</div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $member->registration_count ?? 1 }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold @if(($member->deletion_count ?? 0) > 0) bg-rose-50 text-rose-700 border border-rose-100 @else bg-slate-50 text-slate-600 @endif">
                                {{ $member->deletion_count ?? 0 }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm font-medium text-gray-700 whitespace-nowrap">
                            @if($member->created_at)
                                <div>{{ \Carbon\Carbon::parse($member->created_at)->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($member->created_at)->format('h:i A') }}</div>
                            @else
                                <span class="text-xs text-slate-400">N/A</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-medium text-gray-700">
                            @php
                                $statusDate = 'N/A';
                                if ($member->status === 'approved') {
                                    $rawDate = $member->approved_at ?? $member->approval_date;
                                    $statusDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : 'N/A';
                                } elseif ($member->status === 'blocked') {
                                    $statusDate = $member->blocked_at ? \Carbon\Carbon::parse($member->blocked_at)->format('d/m/Y') : 'N/A';
                                } elseif ($member->status === 'rejected') {
                                    $statusDate = $member->rejected_at ? \Carbon\Carbon::parse($member->rejected_at)->format('d/m/Y') : 'N/A';
                                } elseif ($member->payment_status === 'approved') {
                                    $paidDate = $member->paid_at 
                                        ? \Carbon\Carbon::parse($member->paid_at) 
                                        : ($member->payments()->where('status', 'verified')->latest()->first()?->created_at 
                                            ?? $member->memberships()->latest()->first()?->pivot?->start_date);
                                    $statusDate = $paidDate ? \Carbon\Carbon::parse($paidDate)->format('d/m/Y') : 'N/A';
                                }
                            @endphp
                            {{ $statusDate }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                @if($member->status === 'approved') bg-green-100 text-green-800
                                @elseif($member->status === 'pending') bg-orange-100 text-orange-800
                                @elseif($member->status === 'blocked') bg-red-100 text-red-800
                                @elseif($member->status === 'deleted') bg-rose-100 text-rose-800 border border-rose-200
                                @else bg-slate-100 text-slate-800 @endif">
                                @if($member->status === 'deleted') Account Deleted
                                @else {{ ucfirst($member->status) }} @endif
                            </span>
                        </td>
                    @endif

                    {{-- Actions cell is shared --}}
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.members.show', $member->id) }}" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition" title="Audit Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="{{ route('admin.members.edit', $member->id) }}" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition" title="Edit Profile">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            @if($member->status !== 'approved')
                            <form action="{{ route('admin.members.status', $member->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="p-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition" title="Approve Member">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            @endif

                            @if($member->status !== 'blocked')
                            <form action="{{ route('admin.members.status', $member->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="blocked">
                                <button type="submit" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition" title="Block Member">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this profile?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Delete Profile">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                        No matrimonial members match the search query.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    @if($members->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50 flex items-center justify-between">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection
