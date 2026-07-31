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

<!-- Members Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">Profile</th>
                    <th class="py-3 px-6">Gender</th>
                    <th class="py-3 px-6">Gotra / Native</th>
                    <th class="py-3 px-6">Contact Info</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($members as $member)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="py-4 px-6 flex items-center gap-3">
                        <div class="w-10 h-10 flex-shrink-0 min-w-[40px] aspect-square rounded-full bg-slate-200 overflow-hidden border flex items-center justify-center">
                            @if($member->profile_photo)
                                <img src="/image?file={{ urlencode($member->profile_photo) }}" alt="Photo" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">
                                    {{ substr($member->full_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 leading-tight">
                                <a href="{{ route('admin.members.show', $member->id) }}" class="hover:text-indigo-600 transition">
                                    {{ $member->full_name }}
                                </a>
                            </div>
                            <div class="text-xs text-slate-400 mt-1 font-mono">
                                {{ $member->profile_id ?? 'No Profile ID' }}
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-medium text-gray-600">{{ $member->gender ?? 'N/A' }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-gray-900 font-semibold">{{ $member->gotra ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->native_place ?? 'N/A' }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-gray-900 font-semibold">{{ $member->mobile }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->email }}</div>
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
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.members.show', $member->id) }}" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition" title="Audit Details">
                                <i class="fa-solid fa-eye"></i>
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
