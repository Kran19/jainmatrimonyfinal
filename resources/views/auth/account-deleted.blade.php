@extends('layouts.app')

@section('title', 'Account Deleted - Jain Digambar Matrimony')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg" data-aos="fade-up">
        <div class="bg-white py-10 px-6 shadow-xl sm:rounded-2xl sm:px-10 border border-slate-200/80 text-center">
            
            <!-- Deleted Icon Badge -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border-2 border-rose-200 text-rose-600 mb-6 shadow-inner">
                <i class="fa-solid fa-user-xmark text-3xl"></i>
            </div>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 mb-3">
                <i class="fa-solid fa-circle-xmark"></i> Account Deleted / खाता हटा दिया गया है
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
                Your Account Has Been Deleted
            </h2>
            
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
                Your account and matrimonial profile have been deleted. Your profile details and photos have been removed from our active member listings.
            </p>

            <!-- Deletion Details Box -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-left text-xs space-y-2 mb-6">
                <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                    <span class="text-slate-400 font-medium">Candidate Name:</span>
                    <span class="font-bold text-slate-800">{{ $user->full_name }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                    <span class="text-slate-400 font-medium">Profile ID / Email:</span>
                    <span class="font-mono font-bold text-slate-700">{{ $user->profile_id ?? $user->email }}</span>
                </div>
                @if(!empty($latestReq?->reason) || !empty($user->delete_reason))
                <div class="pt-1">
                    <span class="text-slate-400 font-medium block mb-1">Reason:</span>
                    <div class="bg-white p-2.5 rounded-lg border border-slate-200 text-slate-700 italic">
                        "{{ $latestReq->reason ?? $user->delete_reason }}"
                    </div>
                </div>
                @endif
                <div class="flex justify-between items-center pt-1 text-[11px] text-slate-400">
                    <span>Processed Date:</span>
                    <span>{{ $latestReq?->updated_at ? \Carbon\Carbon::parse($latestReq->updated_at)->format('M d, Y h:i A') : now()->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-slate-800 hover:bg-slate-900 transition shadow-sm">
                        <i class="fas fa-sign-out-alt"></i> Sign Out / Logout
                    </button>
                </form>

                <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition border border-slate-200">
                    <i class="fas fa-home"></i> Return to Homepage
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
