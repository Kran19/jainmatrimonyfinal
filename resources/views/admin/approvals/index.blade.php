@extends('layouts.admin')

@section('title', 'Profile Verification Queue - Admin Panel')
@section('header_title', 'Profile Verification Queue')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 text-lg">Pending Profile Verifications</h3>
        <span class="px-2.5 py-1 bg-orange-50 text-orange-600 text-xs font-bold rounded-full">
            {{ $pendingMembers->total() }} Candidates Awaiting Review
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">Candidate</th>
                    <th class="py-3 px-6">Birth Place / Native</th>
                    <th class="py-3 px-6 font-semibold">Mandir Verification</th>
                    <th class="py-3 px-6">References</th>
                    <th class="py-3 px-6 text-center">Docs Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($pendingMembers as $member)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <!-- Candidate -->
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
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
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="hover:text-indigo-600">
                                        {{ $member->full_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-400 mt-1 font-semibold">
                                    {{ $member->gender }} | {{ $member->email }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Place -->
                    <td class="py-4 px-6">
                        <div class="text-gray-900 font-semibold">{{ $member->birth_place ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->native_place ?? 'N/A' }}</div>
                    </td>

                    <!-- Mandir -->
                    <td class="py-4 px-6">
                        <div class="text-gray-900 font-semibold truncate max-w-xs">{{ $member->mandir_name ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->mandir_address ?? '' }} (Pincode: {{ $member->mandir_pincode ?? '' }})</div>
                    </td>

                    <!-- References -->
                    <td class="py-4 px-6 text-xs space-y-1">
                        @if($member->ref1_name)
                            <div><strong class="text-gray-600">1:</strong> {{ $member->ref1_name }} ({{ $member->ref1_relation }}) - <span class="font-mono">{{ $member->ref1_mobile }}</span></div>
                        @endif
                        @if($member->ref2_name)
                            <div><strong class="text-gray-600">2:</strong> {{ $member->ref2_name }} ({{ $member->ref2_relation }}) - <span class="font-mono">{{ $member->ref2_mobile }}</span></div>
                        @endif
                        @if(!$member->ref1_name && !$member->ref2_name)
                            <span class="text-slate-400">None provided</span>
                        @endif
                    </td>

                    <!-- Docs check status -->
                    <td class="py-4 px-6 text-center text-xs space-y-1 w-28">
                        <div>
                            <span class="px-2 py-0.5 rounded font-bold uppercase {{ $member->profile_photo ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                Photo
                            </span>
                        </div>
                        <div>
                            <span class="px-2 py-0.5 rounded font-bold uppercase {{ $member->id_proof_path ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                ID Proof
                            </span>
                        </div>
                    </td>

                    <!-- Actions -->
                    <td class="py-4 px-6 text-center min-w-[160px]">
                        <div class="flex flex-wrap items-center justify-center gap-1.5">
                            <a href="{{ route('admin.members.show', $member->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                Audit
                            </a>
                            
                            <form action="{{ route('admin.approvals.approve', $member->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition">
                                    Approve
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.approvals.reject', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this profile?');">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                        The verification queue is currently empty.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pendingMembers->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
        {{ $pendingMembers->links() }}
    </div>
    @endif
</div>
@endsection
