@extends('layouts.app')

@section('title', 'Account Deactivated - Jain Digambar Matrimony')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg" data-aos="fade-up">
        <div class="bg-white py-10 px-6 shadow-xl sm:rounded-2xl sm:px-10 border border-slate-200/80 text-center">
            
            <!-- Deactivated Icon Badge -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-amber-50 border-2 border-amber-200 text-amber-600 mb-6 shadow-inner">
                <i class="fa-solid fa-user-slash text-3xl"></i>
            </div>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 mb-3">
                <i class="fa-solid fa-circle-pause"></i> Account Deactivated / खाता निष्क्रिय है
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
                Your Account Has Been Deactivated
            </h2>
            
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
                Your account deactivation request has been approved by the administration. Your candidate profile is currently deactivated and completely hidden from public searches and member listings.
            </p>

            <!-- Deactivation Details Box -->
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
                    <span class="text-slate-400 font-medium block mb-1">Deactivation Reason Submitted:</span>
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

            <!-- Notice about reactivation -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left rounded-r-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2.5 flex-shrink-0"></i>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        If you wish to reactivate your profile in the future, please reach out to our administrative support on WhatsApp or Email. We can quickly restore your active status.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="https://wa.me/917575005121?text=Hello%20Admin,%20my%20account%20({{ urlencode($user->profile_id ?? $user->email) }})%20is%20deactivated.%20I%20would%20like%20to%20request%20reactivation."
                   target="_blank"
                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                    <i class="fab fa-whatsapp text-lg"></i> Contact Admin on WhatsApp
                </a>

                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition border border-slate-200">
                        <i class="fas fa-sign-out-alt"></i> Sign Out / Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
