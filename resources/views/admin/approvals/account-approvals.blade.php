@extends('layouts.admin')

@section('title', 'Account Approvals (Stage 1) - Admin Panel')
@section('header_title', 'Account Approvals (Stage 1)')

@section('content')
<div class="mb-6">
    <p class="text-gray-500 text-sm">Review and approve new basic account registration requests before they can log in and complete the profile wizard.</p>
</div>

<!-- Filters Bar -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.account-approvals.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by Name, Email or Mobile" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm">
                Search
            </button>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">User Info</th>
                    <th class="py-3 px-6">Contact Info</th>
                    <th class="py-3 px-6">Request Date</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse ($members as $member)
                <tr class="hover:bg-slate-50/50 transition duration-150">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->full_name) }}" class="w-full h-full object-cover" alt="Profile Photo">
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 leading-tight">{{ $member->full_name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-gray-800"><i class="fa-solid fa-envelope text-slate-400 w-4"></i> {{ $member->email ?? 'N/A' }}</p>
                        @if($member->mobile)
                            <p class="text-gray-500 text-xs mt-1 font-mono"><i class="fa-solid fa-phone text-slate-400 w-4"></i> {{ $member->mobile }}</p>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-gray-500 font-medium">
                        {{ $member->created_at->format('M d, Y') }}<br>
                        <span class="text-xs text-slate-400 mt-0.5 block">{{ $member->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">Account Pending</span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('admin.account-approvals.approve', $member->id) }}" method="POST" onsubmit="return confirmApproval(event, this)">
                                @csrf
                                <button type="submit" class="p-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-lg transition" title="Approve Account">
                                    <i class="fa-solid fa-check-to-slot"></i>
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.account-approvals.reject', $member->id) }}" method="POST" onsubmit="return confirmRejection(event, this)">
                                @csrf
                                <button type="submit" class="p-2 bg-red-50 hover:bg-red-600 text-red-500 hover:text-white rounded-lg transition" title="Deny & Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-gray-400 font-medium">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-4xl text-emerald-500"></i>
                            <span class="text-gray-600 text-base">No account registration requests pending!</span>
                            <span class="text-xs text-gray-400">All Stage 1 accounts are approved.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
        {{ $members->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>

<script>
function confirmApproval(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Approve Account?',
        text: 'The candidate will now receive an email and will be able to log in to complete the profile wizard.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: 'Yes, approve!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

function confirmRejection(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Deny & Delete Request?',
        text: 'This will completely delete this candidate registration request from the database.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endsection
