@extends('layouts.admin')

@section('title', 'Payment Receipts & Billing - Admin Panel')
@section('header_title', 'Billing & Payment Approvals')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Payments List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Transaction Ledger</h3>
                <span class="text-xs font-bold text-slate-400">Showing (Page {{ $payments->currentPage() }})</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                            <th class="py-3 px-6">Member</th>
                            <th class="py-3 px-6">Plan / Amount</th>
                            <th class="py-3 px-6">Transaction Details</th>
                            <th class="py-3 px-6">Screenshot</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900 leading-tight">
                                    {{ $payment->user->full_name ?? 'Unknown User' }}
                                </div>
                                <div class="text-xs text-slate-400 mt-1 font-mono">
                                    {{ $payment->user->profile_id ?? 'No Profile ID' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $payment->membership->plan_name ?? 'Custom Plan' }}</div>
                                <div class="font-bold text-green-700 mt-0.5">₹{{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-mono text-xs text-gray-700">{{ $payment->transaction_id ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ $payment->payment_method }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($payment->payment_screenshot)
                                    <a href="/image?file={{ urlencode($payment->payment_screenshot) }}" target="_blank">
                                        <img src="/image?file={{ urlencode($payment->payment_screenshot) }}" alt="Receipt" class="w-10 h-10 object-cover rounded border shadow-sm hover:opacity-85 transition">
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    @if($payment->status === 'verified') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($payment->status === 'pending')
                                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="space-y-1">
                                        @csrf
                                        <input type="text" name="remarks" placeholder="Internal remarks..." class="px-2 py-1 border rounded text-xs w-full mb-1">
                                        <div class="flex gap-1">
                                            <button type="submit" name="action" value="approve" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition">
                                                Approve
                                            </button>
                                            <button type="submit" name="action" value="reject" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold transition">
                                                Reject
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-xs text-slate-400">
                                        Verified by: {{ $payment->verifier->name ?? 'System' }}
                                        @if($payment->payment_remarks)
                                            <div class="italic mt-0.5 font-sans">"{{ $payment->payment_remarks }}"</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                                No payment transactions recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Record Manual Payment -->
    <div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Record Offline Payment
            </h3>

            <form action="{{ route('admin.payments.manual') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="profile_id" class="block font-semibold text-gray-700 mb-1">Candidate Profile ID</label>
                    <input type="text" name="profile_id" id="profile_id" required placeholder="e.g. JDM123456"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="membership_id" class="block font-semibold text-gray-700 mb-1">Select Membership Plan</label>
                    <select name="membership_id" id="membership_id" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choose Plan...</option>
                        @foreach($memberships as $membership)
                            <option value="{{ $membership->id }}">
                                {{ $membership->plan_name }} (₹{{ $membership->price }}, {{ $membership->duration_days }} days)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="amount" class="block font-semibold text-gray-700 mb-1">Payment Amount (INR)</label>
                    <input type="number" step="0.01" name="amount" id="amount" required placeholder="e.g. 1500"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="transaction_id" class="block font-semibold text-gray-700 mb-1">Transaction ID / Reference</label>
                    <input type="text" name="transaction_id" id="transaction_id" required placeholder="e.g. TXN98765432"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="payment_method" class="block font-semibold text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" id="payment_method" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI / QR Code">UPI / QR Code</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div>
                    <label for="payment_remarks" class="block font-semibold text-gray-700 mb-1">Remarks</label>
                    <textarea name="payment_remarks" id="payment_remarks" rows="2" placeholder="Receipt details, collected by..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150">
                    Activate Membership Plan
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
