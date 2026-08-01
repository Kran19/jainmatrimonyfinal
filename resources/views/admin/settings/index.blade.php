@extends('layouts.admin')

@section('title', 'Admin Settings - Admin Panel')
@section('header_title', 'Configure Global Platform Settings & Dynamic Pages')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Top Action Bar -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Admin Settings Dashboard</h3>
            <p class="text-gray-500 text-xs mt-0.5">Manage global configuration, homepage content, payment parameters, and legal policies.</p>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition duration-150 shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Save All Settings
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-6 mb-8">

        <!-- 1. Global Platform Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-globe text-indigo-500"></i> Global Platform Settings
                </h4>
            </div>
            <div class="p-6 space-y-6">

                <!-- Toggle: Payment Enabled -->
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm">Enable Payment on Registration</h5>
                        <p class="text-xs text-slate-500 mt-1">If enabled, candidates must pay the subscription fee before completing profile registration.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="hidden" name="payment_enabled" value="0">
                        <input type="checkbox" name="payment_enabled" value="1" {{ ($settings['payment_enabled'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <hr class="border-gray-100">

                <!-- Toggle: Matrimony Book Fee Notice -->
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm">Show Matrimony Book Fee Notice (Homepage)</h5>
                        <p class="text-xs text-slate-500 mt-1">Display a notice on the homepage regarding the Rs. 1000/- fee for printing photos in the matrimony book.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="hidden" name="show_matrimony_book_fee" value="0">
                        <input type="checkbox" name="show_matrimony_book_fee" value="1" {{ ($settings['show_matrimony_book_fee'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <hr class="border-gray-100">

                <!-- Payment QR Code Image Upload -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex-grow">
                        <h5 class="font-bold text-gray-800 text-sm">Payment QR Code Image</h5>
                        <p class="text-xs text-slate-500 mt-1">Upload the QR code image for payments (displayed during payment verification).</p>
                        @if(!empty($settings['payment_qr_code']))
                            <div class="mt-3">
                                @if(str_starts_with($settings['payment_qr_code'], 'data:image/'))
                                    <img src="{{ $settings['payment_qr_code'] }}" alt="QR Code" class="w-24 h-24 object-cover border rounded-xl shadow-sm">
                                @else
                                    <img src="/image?file={{ urlencode(ltrim(str_replace('../', '', $settings['payment_qr_code']), '/\\')) }}" alt="QR Code" class="w-24 h-24 object-cover border rounded-xl shadow-sm" onerror="this.src='https://placehold.co/200x200/fef08a/854d0e?text=QR+Code';">
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="payment_qr_code_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Toggle: Auto-Approve Profiles -->
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm">Auto-Approve Candidate Registrations</h5>
                        <p class="text-xs text-slate-500 mt-1">Automatically approve new candidate profiles without requiring manual admin verification.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="hidden" name="auto_approve" value="0">
                        <input type="checkbox" name="auto_approve" value="1" {{ ($settings['auto_approve'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <hr class="border-gray-100">

                <!-- Toggle: Show Advertisements -->
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm">Show Advertisements Section</h5>
                        <p class="text-xs text-slate-500 mt-1">Display the advertisements section below Free Registration on the homepage.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="hidden" name="show_home_top_ads" value="0">
                        <input type="checkbox" name="show_home_top_ads" value="1" {{ ($settings['show_home_top_ads'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <hr class="border-gray-100">

                <!-- Support Email Address -->
                <div>
                    <label class="block font-bold text-gray-800 text-xs uppercase mb-1">Support Email Address (Admin Notifications)</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

            </div>
        </div>

        <!-- 2. Homepage & Hero Section Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-house text-indigo-500"></i> Homepage & Hero Section Configurations
                </h4>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Homepage Header Title</label>
                    <input type="text" name="home_title" value="{{ $settings['home_title'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Homepage Tagline (Navbar Subtitle)</label>
                    <input type="text" name="home_tagline" value="{{ $settings['home_tagline'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Hero Section Heading (HTML Supported)</label>
                    <textarea name="hero_heading" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['hero_heading'] ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Hero Section Description</label>
                    <textarea name="hero_description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['hero_description'] ?? '' }}</textarea>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                    <div class="flex-grow">
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Hero Center Banner Image</label>
                        <p class="text-xs text-slate-500 mb-2">Upload the main image for the hero section center banner.</p>
                        @if(!empty($settings['hero_banner']))
                            <div class="mt-2">
                                @if(str_starts_with($settings['hero_banner'], 'data:image/'))
                                    <img src="{{ $settings['hero_banner'] }}" alt="Hero Banner" class="w-32 h-auto object-cover border rounded-xl shadow-sm">
                                @else
                                    <img src="/image?file={{ urlencode(ltrim(str_replace('../', '', $settings['hero_banner']), '/\\')) }}" alt="Hero Banner" class="w-32 h-auto object-cover border rounded-xl shadow-sm" onerror="this.src='https://placehold.co/300x150/e0e7ff/3730a3?text=Banner';">
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="hero_banner_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 mt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="font-bold text-indigo-900 text-sm"><i class="fa-solid fa-bullhorn text-indigo-600 mr-1.5"></i> Hero Side Ad Display Controls</h5>
                        <a href="{{ route('admin.cms.ads.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                            <i class="fa-solid fa-rectangle-ad"></i> Manage Ads
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-indigo-50">
                            <span class="text-xs font-semibold text-gray-700">Left Side Ads</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="show_hero_left_ad" value="0">
                                <input type="checkbox" name="show_hero_left_ad" value="1" {{ ($settings['show_hero_left_ad'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-indigo-50">
                            <span class="text-xs font-semibold text-gray-700">Right Side Ads</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="show_hero_right_ad" value="0">
                                <input type="checkbox" name="show_hero_right_ad" value="1" {{ ($settings['show_hero_right_ad'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-indigo-50">
                            <span class="text-xs font-semibold text-gray-700">Bottom Banner Ads</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="show_hero_bottom_ad" value="0">
                                <input type="checkbox" name="show_hero_bottom_ad" value="1" {{ ($settings['show_hero_bottom_ad'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. Public Contact Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-indigo-500"></i> Public Contact Information
                </h4>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Public Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Public Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Public Contact Address</label>
                    <textarea name="contact_address" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['contact_address'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- 4. Dynamic Pages (About Us, Terms & Privacy) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-indigo-500"></i> Dynamic Page Contents & Policies
                </h4>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">About Us YouTube Video URL</label>
                    <input type="text" name="about_youtube" value="{{ $settings['about_youtube'] ?? '' }}" placeholder="https://www.youtube.com/embed/..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">This YouTube embed video is displayed on the public About Us page.</p>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">About Us Page Content (HTML Supported)</label>
                    <textarea name="about_us" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['about_us'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Terms & Conditions (HTML Supported)</label>
                    <textarea name="terms_conditions" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['terms_conditions'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Privacy Policy (HTML Supported)</label>
                    <textarea name="privacy_policy" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['privacy_policy'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Save Action -->
    <div class="flex justify-end pb-8">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-sm font-bold transition duration-150 shadow-md flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Save All Settings
        </button>
    </div>

</form>
@endsection
