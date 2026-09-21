@extends('layouts.admin')

@section('title', 'Deactivation & Deletion Requests - Admin Panel')
@section('header_title', 'Deactivation / Deletion Requests')

@section('content')
<!-- Table of requests -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">Profile</th>
                    <th class="py-3 px-6">Request Type</th>
                    <th class="py-3 px-6">Reason</th>
                    <th class="py-3 px-6">Date</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($requests as $req)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="py-4 px-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border">
                            @if($req->profile_photo)
                                <img src="/image?file={{ urlencode($req->profile_photo) }}" alt="Photo" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">
                                    {{ substr($req->full_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 leading-tight">
                                {{ $req->full_name }}
                            </div>
                            <div class="text-xs text-slate-500 font-medium">
                                {{ $req->email ?? 'No Email' }} | {{ $req->mobile ?? 'No Mobile' }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                {{ $req->profile_id ?? 'No Profile ID' }}
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @if($req->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $req->request_type === 'deletion' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }} flex items-center gap-1 w-fit">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                {{ $req->request_type === 'deletion' ? 'Deletion Request' : 'Deactivation Request' }}
                            </span>
                        @elseif($req->status === 'processed' || $req->user_status === 'deleted')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 w-fit block">
                                {{ $req->request_type === 'deletion' ? 'Account Deleted' : 'Deactivated' }}
                            </span>
                        @elseif($req->status === 'rejected')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 w-fit block">
                                Request Rejected
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 w-fit block">
                                {{ ucfirst($req->request_type) }}
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="max-w-xs md:max-w-md bg-slate-50 border border-slate-200/90 rounded-xl p-3 text-xs leading-relaxed text-slate-800 shadow-sm">
                            <div class="flex items-start gap-1.5">
                                <i class="fa-solid fa-quote-left text-slate-400 text-[11px] mt-0.5 flex-shrink-0"></i>
                                <span class="font-medium whitespace-pre-line">{{ $req->reason ?? 'No reason provided' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-gray-500 text-xs">
                        <div>{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</div>
                        <div class="text-[10px] text-gray-400 font-mono">{{ \Carbon\Carbon::parse($req->created_at)->format('h:i A') }}</div>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                <!-- Approve Button -->
                                <form action="{{ route('admin.members.requests.approve', $req->id) }}" method="POST" onsubmit="return confirmApprove(event, this, '{{ addslashes($req->full_name ?? 'Member') }}', '{{ $req->request_type }}')">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition duration-150 shadow-sm flex items-center gap-1.5" title="{{ $req->request_type === 'deactivation' ? 'Approve deactivation and hide profile' : 'Approve & permanently delete member account' }}">
                                        <i class="fa-solid fa-check"></i> {{ $req->request_type === 'deactivation' ? 'Approve & Deactivate' : 'Approve & Delete' }}
                                    </button>
                                </form>

                                <!-- Reject Button -->
                                <form action="{{ route('admin.members.requests.reject', $req->id) }}" method="POST" onsubmit="return confirmReject(event, this, '{{ addslashes($req->full_name ?? 'Member') }}', '{{ $req->request_type }}')">
                                    @csrf
                                    <button type="submit" class="bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition duration-150 shadow-sm flex items-center gap-1" title="Reject request and keep account active">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($req->status === 'processed' || in_array($req->user_status, ['deleted', 'deactivated', 'blocked']))
                            @if($req->request_type === 'deactivation' || in_array($req->user_status, ['deactivated', 'blocked']))
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200 inline-flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-circle-pause"></i> Deactivated
                                </span>
                            @else
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100 inline-flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Account Deleted
                                </span>
                            @endif
                        @elseif($req->status === 'rejected')
                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 inline-flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Request Rejected
                            </span>
                        @else
                            <span class="text-xs font-bold text-slate-400">
                                {{ ucfirst($req->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-gray-400 font-medium">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-4xl text-emerald-500"></i>
                            <span class="text-gray-600 text-base">No pending deactivation or deletion requests!</span>
                            <span class="text-xs text-gray-400">All member profile closure requests are up to date.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmApprove(e, form, memberName, type) {
    e.preventDefault();
    const isDeactivation = (type === 'deactivation');
    const title = isDeactivation ? 'Approve Deactivation Request?' : 'Approve Deletion Request?';
    const actionDesc = isDeactivation
        ? `deactivate the profile of "${memberName}". They will be hidden from searches and their account deactivated`
        : `permanently delete the member account of "${memberName}". They will be removed from all searches and cannot log in`;
    const btnText = isDeactivation ? 'Yes, Approve & Deactivate' : 'Yes, Approve & Delete';

    Swal.fire({
        title: title,
        text: `Are you sure you want to approve this request? This will ${actionDesc}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: btnText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

function confirmReject(e, form, memberName, type) {
    e.preventDefault();
    const reqWord = type === 'deactivation' ? 'deactivation' : 'deletion';
    Swal.fire({
        title: `Reject ${type === 'deactivation' ? 'Deactivation' : 'Deletion'} Request?`,
        text: `Are you sure you want to reject the ${reqWord} request for "${memberName}"? Their account will remain active.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Reject Request',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endsection
