@extends('layouts.app')

@section('title', 'Forgot Password - Jain Digambar Matrimony')

@section('content')
<main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 border border-gray-100" data-aos="fade-up">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Forgot Password</h2>
            <p class="text-gray-600 mt-2">Enter your registered email address and we'll send you a link to reset your password.</p>
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
        @else
            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                               class="w-full border border-gray-300 rounded-lg pl-10 px-4 py-2 focus:ring-primary focus:border-primary bg-gray-50 focus:outline-none"
                               placeholder="you@example.com">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg hover:bg-opacity-90 transition font-medium">
                    Send Reset Link
                </button>
            </form>
        @endif

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Remember your password? <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Back to Login</a>
            </p>
        </div>
    </div>
</main>
@endsection
