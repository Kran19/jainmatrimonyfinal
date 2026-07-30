@extends('layouts.admin')

@section('title', 'Incomplete Registrations - Admin Panel')
@section('header_title', 'Incomplete Registrations')

@section('content')
<!-- Filter Panel -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
    <form action="{{ route('admin.members.incomplete') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-grow">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Profile</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile..."
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition duration-150">
                Search
            </button>
            <a href="{{ route('admin.members.incomplete') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg text-sm transition duration-150 text-center">
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
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-6">Contact Info</th>
                    <th class="py-3 px-6">Stage 1 Date</th>
                    <th class="py-3 px-6">Days Incomplete</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($members as $member)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-900 leading-tight">
                            {{ $member->full_name }}
                        </div>
                        <span class="inline-block mt-1 bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded text-[10px] font-bold">
                            Stage 1 Approved
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-gray-900 font-semibold">{{ $member->mobile }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->email }}</div>
                    </td>
                    <td class="py-4 px-6 text-gray-500">
                        {{ $member->updated_at ? $member->updated_at->format('M d, Y H:i') : ($member->created_at ? $member->created_at->format('M d, Y H:i') : 'N/A') }}
                    </td>
                    <td class="py-4 px-6 font-bold text-slate-600">
                        @php
                            $days = $member->updated_at ? now()->diffInDays($member->updated_at) : ($member->created_at ? now()->diffInDays($member->created_at) : 0);
                        @endphp
                        {{ $days }} {{ Str::plural('day', $days) }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirmDelete(event, this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition duration-150" title="Delete record">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
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
                            <span class="text-gray-600 text-base">No incomplete registrations found!</span>
                            <span class="text-xs text-gray-400">All registered users have completed their profiles.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($members->hasPages())
    <div class="bg-slate-50 px-6 py-4 border-t border-gray-100">
        {{ $members->links() }}
    </div>
    @endif
</div>

<script>
function confirmDelete(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Delete Incomplete User?',
        text: 'This will completely delete this user record from the database. This action is irreversible.',
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
