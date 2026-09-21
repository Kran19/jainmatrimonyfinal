@extends('layouts.app')

@section('title', 'Waiting for Approval - Jain Digambar Matrimony')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md" data-aos="fade-up">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100 text-center">
            
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6 animate-pulse">
                <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Waiting for Approval</h2>
            
            <p class="text-gray-600 mb-6 text-sm leading-relaxed">
                Your account is currently under review by our administration team. 
                You will be able to access the platform once your account has been approved.
            </p>
            
            @if(isset($latestReq) && $latestReq && $latestReq->status === 'pending')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 mb-5 text-left text-xs">
                <div class="font-bold text-amber-800 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-clock-rotate-left text-amber-600"></i>
                    {{ $latestReq->request_type === 'deactivation' ? 'Deactivation Request Under Review' : 'Deletion Request Under Review' }}
                </div>
                <div class="text-slate-600">
                    Your request has been submitted to administration. Once approved, this page will automatically refresh.
                </div>
                @if(!empty($latestReq->reason))
                <div class="mt-2 text-[11px] text-slate-500 italic bg-white/80 p-2 rounded border border-amber-100">
                    "{{ $latestReq->reason }}"
                </div>
                @endif
            </div>
            @else
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Approvals usually take between 24-48 hours. Thank you for your patience!
                        </p>
                    </div>
                </div>
            </div>
            @endif

            @auth('web')
            <div class="space-y-3">
                <a href="{{ route('profile.my') }}" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition">
                    View My Form
                </a>
                
                <form action="{{ route('profile.delete') }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="delete_reason" value="Deleted while waiting for approval">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete your profile? This action is permanent.')" class="w-full flex justify-center py-2.5 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Delete My Profile
                    </button>
                </form>

                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            <!-- Auto-refresh polling script -->
            <script>
            (function() {
                const initialStatus = "{{ Auth::guard('web')->user()->status ?? '' }}";
                const initialReqStatus = "{{ $latestReq->status ?? '' }}";
                
                setInterval(async function() {
                    try {
                        const response = await fetch("{{ route('api.account.status-check') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (!data.authenticated) {
                                window.location.href = "{{ route('login') }}";
                                return;
                            }
                            // If status or request status changed, reload immediately to display updated page
                            if (data.status !== initialStatus || (data.request_status && data.request_status !== initialReqStatus)) {
                                window.location.reload();
                            }
                        }
                    } catch (err) {
                        // ignore network dropouts
                    }
                }, 5000);
            })();
            </script>
            @else
            <div>
                <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Return to Login</a>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection
