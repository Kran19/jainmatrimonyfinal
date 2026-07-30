@extends('layouts.app')

@section('title', 'Set New Password - Jain Digambar Matrimony')

@section('content')
<main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 border border-gray-100" data-aos="fade-up">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Set New Password</h2>
            <p class="text-gray-600 mt-2">Enter your new password below.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-block bg-primary text-white py-2 px-6 rounded-lg hover:bg-opacity-90 transition font-medium">
                    Go to Login
                </a>
            </div>
        @else
            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg pl-10 px-4 py-2 focus:ring-primary focus:border-primary bg-gray-50 focus:outline-none"
                               placeholder="Min. 8 characters">
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg pl-10 px-4 py-2 focus:ring-primary focus:border-primary bg-gray-50 focus:outline-none"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg hover:bg-opacity-90 transition font-medium">
                    Reset Password
                </button>
            </form>
        @endif
    </div>
</main>
@endsection
