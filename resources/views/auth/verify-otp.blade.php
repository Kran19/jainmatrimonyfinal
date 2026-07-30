@extends('layouts.app')

@section('title', 'Verify OTP - Jain Digambar Matrimony')

@section('content')
<section class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-dark" data-aos="fade-up">
                Email Verification
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600" data-aos="fade-up" data-aos-delay="100">
                Please enter the 6-digit verification code.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
                
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                        <p class="text-sm text-green-700 font-medium">{!! session('success') !!}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                @if(session('reg_data'))
                    <p class="text-sm text-gray-600 mb-4">Enter the 6-digit OTP sent to <strong>{{ session('reg_data')['email'] }}</strong></p>
                @endif

                <form action="{{ route('register.otp') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="otp_code" class="block text-sm font-medium text-gray-700 text-center">OTP Code *</label>
                        <div class="mt-1">
                            <input id="otp_code" name="otp_code" type="text" maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                   required autocomplete="one-time-code" placeholder="123456"
                                   style="letter-spacing: 0.25em;"
                                   class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm text-center text-2xl tracking-widest focus:outline-none focus:ring-primary focus:border-primary sm:text-sm bg-gray-50 font-bold">
                        </div>
                        @error('otp_code') <span class="text-red-500 text-xs mt-1 block text-center">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition">
                        Verify OTP & Create Account
                    </button>
                </form>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col items-center gap-3 text-sm">
                    <form action="{{ route('register.resend-otp') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center text-primary hover:text-opacity-80 font-bold hover:underline transition bg-transparent border-0 cursor-pointer text-sm">
                            <i class="fas fa-redo-alt text-xs mr-1.5"></i> Resend OTP Code
                        </button>
                    </form>

                    <a href="{{ route('register') }}" class="text-xs text-gray-500 hover:text-gray-700 transition">
                        ← Change Email or Mobile Number
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
