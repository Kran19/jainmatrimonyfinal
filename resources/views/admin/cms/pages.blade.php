@extends('layouts.admin')

@section('title', 'About Us & Pages CMS - Admin Panel')
@section('header_title', 'About Us & Dynamic Pages CMS')

@section('content')
<form action="{{ route('admin.cms.pages.update') }}" method="POST" class="space-y-6">
    @csrf

    <!-- Top Action Bar -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2.5">
                <i class="fa-solid fa-file-signature text-indigo-600"></i>
                <span>About Us & Dynamic Pages Content Management</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Edit website page text, Hindi/English About Us messages, YouTube video embed, Terms & Conditions, and Privacy Policy.
            </p>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold px-6 py-2.5 rounded-xl text-sm shadow-md hover:shadow-lg transition duration-150 flex items-center gap-2 flex-shrink-0">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save All Pages</span>
        </button>
    </div>

    <!-- 1. About Us Section (Hindi & English + YouTube) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-book-open text-indigo-500"></i> About Us Page Content (परिचय एवं संदेश)
            </h4>
            <a href="{{ url('/about') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                <span>View Live About Page</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>
        <div class="p-6 space-y-6">

            <!-- YouTube Video Embed URL -->
            <div>
                <label class="block font-bold text-gray-800 text-xs uppercase mb-1">
                    About Us YouTube Video Embed URL <span class="text-slate-400 font-normal">(यूट्यूब वीडियो लिंक)</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-red-500">
                        <i class="fa-brands fa-youtube text-base"></i>
                    </span>
                    <input type="text" name="about_youtube" id="about_youtube_input" value="{{ $settings['about_youtube'] ?? '' }}" 
                           placeholder="https://www.youtube.com/embed/VIDEO_ID"
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Provide a valid YouTube embed URL (e.g. <code class="text-indigo-600">https://www.youtube.com/embed/dQw4w9WgXcQ</code>) to showcase on the About Us page.</p>

                @if(!empty($settings['about_youtube']))
                <div class="mt-3 bg-slate-50 p-3 rounded-xl border border-gray-200 max-w-md">
                    <p class="text-xs font-bold text-slate-600 mb-2">Video Preview:</p>
                    <div class="aspect-video w-full rounded-lg overflow-hidden bg-black">
                        <iframe src="{{ $settings['about_youtube'] }}" class="w-full h-full border-0" allowfullscreen></iframe>
                    </div>
                </div>
                @endif
            </div>

            <hr class="border-gray-100">

            <!-- About Us Hindi -->
            <div>
                <label class="block font-bold text-gray-800 text-xs uppercase mb-1">
                    About Us Content (Hindi / हिंदी - HTML Supported) <span class="text-slate-400 font-normal">(मुख्य संदेश)</span>
                </label>
                <textarea name="about_us" rows="7" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-sans leading-relaxed">{{ $settings['about_us'] ?? '' }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Displayed as the primary Hindi message from the President/Committee on the About Us page.</p>
            </div>

            <!-- About Us English -->
            <div>
                <label class="block font-bold text-gray-800 text-xs uppercase mb-1">
                    About Us Content (English - HTML Supported) <span class="text-slate-400 font-normal">(अंग्रेजी संदेश)</span>
                </label>
                <textarea name="about_us_en" rows="7" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-sans leading-relaxed">{{ $settings['about_us_en'] ?? '' }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Displayed under the English tab on the About Us page.</p>
            </div>

        </div>
    </div>

    <!-- 2. Community Initiatives Page -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-people-group text-indigo-500"></i> Community Initiatives Page (समाज पहल)
            </h4>
            <a href="{{ url('/community') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                <span>View Live Community Page</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block font-bold text-gray-800 text-xs uppercase mb-1">
                    Community Page Content (HTML & Tailwind Supported)
                </label>
                <textarea name="community_content" rows="6" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono text-xs">{{ $settings['community_content'] ?? '' }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Custom HTML content for the Community Initiatives page.</p>
            </div>
        </div>
    </div>

    <!-- 3. Legal & Policy Pages (Terms & Conditions and Privacy Policy) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500"></i> Legal Policies & Compliance
            </h4>
        </div>
        <div class="p-6 space-y-6">

            <!-- Terms & Conditions -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-gray-800 text-xs uppercase">
                        Terms & Conditions (नियम एवं शर्तें - HTML Supported)
                    </label>
                    <a href="{{ url('/terms') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                        <span>View Live Terms</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
                <textarea name="terms_conditions" rows="8" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-sans leading-relaxed">{{ $settings['terms_conditions'] ?? '' }}</textarea>
            </div>

            <hr class="border-gray-100">

            <!-- Privacy Policy -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-gray-800 text-xs uppercase">
                        Privacy Policy (गोपनीयता नीति - HTML Supported)
                    </label>
                    <a href="{{ url('/privacy') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                        <span>View Live Privacy Policy</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
                <textarea name="privacy_policy" rows="8" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-sans leading-relaxed">{{ $settings['privacy_policy'] ?? '' }}</textarea>
            </div>

        </div>
    </div>

    <!-- 4. Organization Contact & Corporate Address -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-building text-indigo-500"></i> Corporate Contact Information (Footer & Contact Page)
            </h4>
            <a href="{{ url('/contact') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                <span>View Live Contact Page</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-800 text-xs uppercase mb-1">Official Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'digambarjainparichay@gmail.com' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-800 text-xs uppercase mb-1">Official Contact Phone / Helpline</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 7575005121' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-800 text-xs uppercase mb-1">Corporate Office Address</label>
                <textarea name="contact_address" rows="3" 
                          class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-sans">{{ $settings['contact_address'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Bottom Submit Button -->
    <div class="flex justify-end pt-2">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold px-8 py-3 rounded-xl text-sm shadow-md hover:shadow-lg transition duration-150 flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save All Pages & Policies</span>
        </button>
    </div>

</form>
@endsection
