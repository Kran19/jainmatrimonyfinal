@extends('layouts.admin')

@section('title', 'Payment Receipts & Billing - Admin Panel')
@section('header_title', 'Billing & Payment Approvals')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left Column: Payments List -->
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-green-800 text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-3 text-red-800 text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-4.293-4.293a1 1 0 011.414 0L10 11.414l2.879 2.879a1 1 0 01-1.414 1.414L10 12.828l-2.879 2.879a1 1 0 01-1.414-1.414L8.586 10 5.707 7.121a1 1 0 111.414-1.414L10 8.586l2.879-2.879a1 1 0 111.414 1.414L11.414 10l2.879 2.879a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header + filter tabs --}}
            <div class="p-5 border-b border-gray-100 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="font-bold text-gray-800 text-lg">Transaction Ledger</h3>
                    <div class="flex gap-1 text-xs font-bold flex-wrap">
                        <a href="{{ route('admin.payments.index', array_merge(request()->except('status'), ['status' => ''])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('admin.payments.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                           class="px-3 py-1.5 rounded-lg transition flex items-center gap-1 {{ $statusFilter === 'pending' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
                            Pending
                            @if($pendingCount > 0)
                                <span class="bg-red-500 text-white rounded-full px-1.5 py-0.5 text-[10px] leading-none font-black">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.payments.index', array_merge(request()->except('status'), ['status' => 'verified'])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'verified' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                            Approved ({{ $verifiedCount }})
                        </a>
                        <a href="{{ route('admin.payments.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'rejected' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                            Rejected ({{ $rejectedCount }})
                        </a>
                    </div>
                </div>

                {{-- Search & Date Filters --}}
                <form action="{{ route('admin.payments.index') }}" method="GET"
                      class="flex flex-wrap items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    {{-- Search bar (wider) --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search name, ID, phone, TXN..."
                           class="flex-[2] min-w-[140px] px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    {{-- From date --}}
                    <input type="date" name="from_date" value="{{ request('from_date') }}" title="From Date"
                           class="flex-1 min-w-[120px] px-2 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    {{-- To date --}}
                    <input type="date" name="to_date" value="{{ request('to_date') }}" title="To Date"
                           class="flex-1 min-w-[120px] px-2 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    {{-- Apply --}}
                    <button type="submit"
                            class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition">
                        Apply
                    </button>
                    {{-- Clear — always visible --}}
                    <a href="{{ route('admin.payments.index', request('status') ? ['status' => request('status')] : []) }}"
                       class="shrink-0 inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 hover:text-red-700 font-bold px-3 py-1.5 rounded-lg text-xs transition"
                       title="Clear all filters">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear
                    </a>
                </form>
            </div>{{-- end p-5 header --}}

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                            <th class="py-3 px-5">Member</th>
                            <th class="py-3 px-5">Plan / Amount</th>
                            <th class="py-3 px-5">Method</th>
                            <th class="py-3 px-5">Screenshot</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            {{-- Member info --}}
                            <td class="py-4 px-5">
                                <div class="font-bold text-gray-900 leading-tight">
                                    {{ $payment->user->full_name ?? $payment->full_name ?? 'Unknown User' }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5 font-mono">
                                    {{ $payment->user->profile_id ?? 'No Profile ID' }}
                                </div>
                                @if($payment->user?->mobile ?? $payment->phone_number)
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $payment->user?->mobile ?? $payment->phone_number }}
                                </div>
                                @endif
                            </td>

                            {{-- Plan / Amount --}}
                            <td class="py-4 px-5">
                                @if($payment->membership)
                                    <div class="font-bold text-slate-900">{{ $payment->membership->plan_name }}</div>
                                    <div class="font-bold text-green-700 mt-0.5">
                                        ₹{{ number_format($payment->amount, 2) }}
                                    </div>
                                @else
                                    <div class="font-semibold text-indigo-700">Screenshot Upload</div>
                                    <div class="text-xs text-slate-400 mt-0.5 italic">No plan assigned</div>
                                @endif
                            </td>

                            {{-- Method --}}
                            <td class="py-4 px-5">
                                <div class="text-xs text-gray-700 font-medium">{{ $payment->payment_method ?? 'N/A' }}</div>
                                @if($payment->transaction_id && !str_starts_with($payment->transaction_id, 'SCR-'))
                                    <div class="font-mono text-xs text-slate-400 mt-0.5">{{ $payment->transaction_id }}</div>
                                @endif
                                <div class="text-xs text-slate-300 mt-0.5">
                                    {{ $payment->created_at?->format('d M Y') }}
                                </div>
                            </td>

                            {{-- Screenshot --}}
                            <td class="py-4 px-5">
                                @php
                                    // Prefer payment_screenshot from payments table, fallback to user's screenshot
                                    $screenshotPath = $payment->payment_screenshot
                                        ?? $payment->user?->payment_screenshot;
                                @endphp
                                @if($screenshotPath)
                                    <a href="/image?file={{ urlencode($screenshotPath) }}" target="_blank" title="View full screenshot">
                                        <img src="/image?file={{ urlencode($screenshotPath) }}"
                                             alt="Payment Receipt"
                                             class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm hover:opacity-80 transition">
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300 italic">None</span>
                                @endif
                            </td>

                            {{-- Status badge --}}
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($payment->status === 'verified') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $payment->status === 'verified' ? '✓ Approved' : ucfirst($payment->status) }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td class="py-4 px-5">
                                @if($payment->status === 'pending')
                                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="space-y-1.5 pay-form">
                                        @csrf
                                        <input type="hidden" name="action" value="" class="pay-action-field">
                                        <input type="text" name="remarks" placeholder="Remarks (optional)..."
                                               class="px-2 py-1 border border-gray-200 rounded text-xs w-full focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                        <div class="flex gap-1">
                                            <button type="button"
                                                    class="flex-1 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition"
                                                    onclick="submitPaymentAction(this, 'approve', 'Approve this payment? Member will be marked as Paid.')">
                                                ✓ Approve
                                            </button>
                                            <button type="button"
                                                    class="flex-1 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-bold transition"
                                                    onclick="submitPaymentAction(this, 'reject', 'Reject this payment?')">
                                                ✕ Reject
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-xs text-slate-400 space-y-0.5">
                                        <div>{{ $payment->status === 'verified' ? 'Approved' : 'Rejected' }} by:</div>
                                        <div class="font-semibold text-gray-600">{{ $payment->verifier->name ?? 'System' }}</div>
                                        @if($payment->payment_remarks && $payment->payment_remarks !== 'Auto-created from member screenshot upload.')
                                            <div class="italic">"{{ $payment->payment_remarks }}"</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center">
                                <div class="text-gray-400 text-sm">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    @if($statusFilter)
                                        No {{ $statusFilter }} payment transactions found.
                                    @else
                                        No payment transactions recorded yet.
                                    @endif
                                </div>
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
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-6">
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

            {{-- Info box --}}
            <div class="mt-5 p-3 bg-blue-50 rounded-xl text-xs text-blue-700 leading-relaxed">
                <strong>Note:</strong> Members who uploaded payment screenshots via the registration form appear automatically in the Pending tab above. Approve them here to mark their account as <span class="font-bold">Paid</span>.
            </div>
        </div>
    </div>

</div>

<script>
/**
 * Sets the hidden action field in the payment form and submits it.
 * This is more reliable than button[name][value] + onclick="return confirm()" 
 * which can fail to send the value in some browser/OS combinations.
 */
function submitPaymentAction(btn, action, message) {
    if (!confirm(message)) return;

    // Disable buttons to prevent double-submission
    var form = btn.closest('form.pay-form');
    if (!form) return;

    form.querySelectorAll('button').forEach(function(b) {
        b.disabled = true;
        b.style.opacity = '0.6';
    });

    // Set the hidden action field
    var actionField = form.querySelector('.pay-action-field');
    if (actionField) {
        actionField.value = action;
    }

    // Submit
    form.submit();
}
</script>
@endsection
