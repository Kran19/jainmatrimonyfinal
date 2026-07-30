@extends('layouts.admin')

@section('title', 'System Reports - Admin Panel')
@section('header_title', 'Statistical Reports & Audit')

@section('content')
<style>
    @media print {
        aside, header, .no-print, form, button {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .print-container {
            width: 100% !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
        table {
            border: 1px solid #e2e8f0 !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }
        th, td {
            border: 1px solid #cbd5e1 !important;
            padding: 6px 10px !important;
            font-size: 11px !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
        }
    }
</style>

<!-- Print Header Branding (Hidden on screen) -->
<div class="hidden print-header text-center pb-4 border-b">
    <h2 class="text-2xl font-black text-slate-800">Jain Digambar Matrimony</h2>
    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mt-1">
        Official Management Audit Report
    </p>
    <p class="text-xs text-slate-400 mt-0.5">
        Generated: {{ now()->format('d M Y, H:i') }} | Date Range: {{ $startDate }} to {{ $endDate }}
    </p>
</div>

<!-- Controls Panel -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 no-print">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Report Type</label>
            <select name="report_type" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="members" {{ $reportType === 'members' ? 'selected' : '' }}>Member Registrations</option>
                <option value="revenue" {{ $reportType === 'revenue' ? 'selected' : '' }}>Revenue Audit</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>

        @if($reportType === 'members')
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status (Optional)</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Statuses</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
        </div>
        @else
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Method (Optional)</label>
            <select name="payment_method" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Methods</option>
                <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Bank Transfer" {{ request('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="UPI / QR Code" {{ request('payment_method') === 'UPI / QR Code' ? 'selected' : '' }}>UPI / QR Code</option>
            </select>
        </div>
        @endif

        <div class="flex items-end gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition duration-150 flex-grow">
                Generate
            </button>
            <a href="{{ route('admin.reports.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition duration-150 flex items-center gap-2 shadow-sm" title="Export Excel (CSV)">
                <i class="fa-solid fa-file-excel text-base"></i> Export Excel
            </a>
            <button type="button" onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg text-sm transition duration-150">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </form>
</div>

<!-- Summaries Boxes -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 print-container">
    @if($reportType === 'members')
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h4 class="text-xs font-bold text-slate-400 uppercase">Total Registrations</h4>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h4 class="text-xs font-bold text-slate-400 uppercase">Approved Profiles</h4>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $summary['approved'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h4 class="text-xs font-bold text-slate-400 uppercase">Pending Approvals</h4>
            <p class="text-2xl font-black text-orange-600 mt-1">{{ $summary['pending'] }}</p>
        </div>
    @else
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center md:col-span-2">
            <h4 class="text-xs font-bold text-slate-400 uppercase">Total Revenue Generated</h4>
            <p class="text-2xl font-black text-emerald-600 mt-1">₹{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h4 class="text-xs font-bold text-slate-400 uppercase">Verified Payments</h4>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $summary['total_transactions'] }}</p>
        </div>
    @endif
</div>

<!-- Data Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden print-container">
    <div class="p-6 border-b border-gray-100 no-print">
        <h3 class="font-bold text-gray-800 text-base">Auditable Audit Trail</h3>
    </div>

    @if($reportType === 'members')
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">ID</th>
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-6">Gender</th>
                    <th class="py-3 px-6">Gotra</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6">Date Registered</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($results as $member)
                <tr>
                    <td class="py-3.5 px-6 font-mono font-bold text-xs text-slate-500">{{ $member->profile_id ?? 'N/A' }}</td>
                    <td class="py-3.5 px-6 font-bold text-slate-900">{{ $member->full_name }}</td>
                    <td class="py-3.5 px-6 text-slate-600">{{ $member->gender }}</td>
                    <td class="py-3.5 px-6 text-slate-600">{{ $member->gotra ?? 'N/A' }}</td>
                    <td class="py-3.5 px-6">
                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                            @if($member->status === 'approved') text-green-700 bg-green-50
                            @else text-slate-600 bg-slate-100 @endif">
                            {{ $member->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-slate-400">{{ $member->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 px-6 text-center text-gray-500">No member registrations found in date range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">Date</th>
                    <th class="py-3 px-6">Transaction ID</th>
                    <th class="py-3 px-6">Member</th>
                    <th class="py-3 px-6">Method</th>
                    <th class="py-3 px-6">Amount</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($results as $payment)
                <tr>
                    <td class="py-3.5 px-6 text-slate-500">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-3.5 px-6 font-mono text-xs text-slate-700">{{ $payment->transaction_id }}</td>
                    <td class="py-3.5 px-6 font-bold text-slate-900">{{ $payment->user->full_name ?? 'Unknown Candidate' }}</td>
                    <td class="py-3.5 px-6 text-slate-600">{{ $payment->payment_method }}</td>
                    <td class="py-3.5 px-6 font-black text-green-700">₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-6 text-center text-gray-500">No payment receipts found in date range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>
@endsection
