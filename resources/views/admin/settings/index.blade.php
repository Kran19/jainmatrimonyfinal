@extends('layouts.admin')

@section('title', 'Site Settings - Admin Panel')
@section('header_title', 'Global Site Configurations')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg">System Parameter Tuning</h3>
            <p class="text-xs text-slate-400 mt-1">Configure global settings loaded dynamically across the application.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-6 text-sm">
            @csrf

            <!-- Payment Configurations -->
            <div class="space-y-4">
                <h4 class="font-bold text-gray-900 text-sm border-l-2 border-indigo-500 pl-2">Payment Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="upi_id" class="block font-semibold text-gray-700 mb-1">Merchant UPI ID</label>
                        <input type="text" name="upi_id" id="upi_id" value="{{ $settings['upi_id'] }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">Target merchant ID displayed on candidate payment step.</p>
                    </div>
                </div>
            </div>

            <!-- Helpdesk Configurations -->
            <div class="space-y-4 pt-4 border-t">
                <h4 class="font-bold text-gray-900 text-sm border-l-2 border-indigo-500 pl-2">Support Helpline Contacts</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="support_phone" class="block font-semibold text-gray-700 mb-1">Helpline Phone Number</label>
                        <input type="text" name="support_phone" id="support_phone" value="{{ $settings['support_phone'] }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="support_email" class="block font-semibold text-gray-700 mb-1">Support Email Address</label>
                        <input type="email" name="support_email" id="support_email" value="{{ $settings['support_email'] }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Candidate Defaults -->
            <div class="space-y-4 pt-4 border-t">
                <h4 class="font-bold text-gray-900 text-sm border-l-2 border-indigo-500 pl-2">System Defaults</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_registration_age" class="block font-semibold text-gray-700 mb-1">Minimum Registration Age</label>
                        <input type="number" name="min_registration_age" id="min_registration_age" value="{{ $settings['min_registration_age'] }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 shadow-sm">
                    Save Configurations
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
