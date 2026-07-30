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
                            <div class="text-xs text-slate-400 mt-1 font-mono">
                                {{ $req->profile_id ?? 'No Profile ID' }}
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            @if($req->request_type === 'deletion') bg-red-100 text-red-800
                            @else bg-amber-100 text-amber-800 @endif">
                            {{ ucfirst($req->request_type) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-gray-600 font-medium italic">
                        "{{ $req->reason ?? 'No reason provided' }}"
                    </td>
                    <td class="py-4 px-6 text-gray-500">
                        {{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <form action="{{ route('admin.members.requests.process', $req->id) }}" method="POST" onsubmit="return confirmProcess(event, this, '{{ $req->request_type }}')">
                            @csrf
                            <button type="submit" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition duration-150 shadow-sm border border-indigo-100">
                                Process Request
                            </button>
                        </form>
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
function confirmProcess(e, form, type) {
    e.preventDefault();
    const actionText = type === 'deletion' ? 'COMPLETELY DELETE this user from the database' : 'BLOCK this user profile';
    Swal.fire({
        title: 'Process Request?',
        text: `Are you sure you want to process this request? This will ${actionText}. This action is irreversible.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: 'Yes, process it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endsection
