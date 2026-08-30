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

                <!-- Payment Management Section -->
                <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100 space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h5 class="font-bold text-gray-900 text-sm">Registration Payment (पंजीकरण शुल्क)</h5>
                                @if(($settings['payment_enabled'] ?? '0') == '1')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check"></i> Enabled (₹{{ number_format((float)($settings['registration_fee'] ?? 0), 0) }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-ban"></i> Disabled (Free Registration)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">When turned ON, candidates must pay the specified registration fee and upload proof to complete registration. When turned OFF, registration is 100% free and no payment screen is shown.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input type="hidden" name="payment_enabled" value="0">
                            <input type="checkbox" name="payment_enabled" id="payment_enabled_toggle" value="1" {{ ($settings['payment_enabled'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-indigo-100">
                        <div>
                            <label class="block font-bold text-gray-800 text-xs uppercase mb-1">Registration Fee Amount (₹) <span class="text-slate-400 font-normal">(शुल्क राशि)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-bold text-sm">₹</span>
                                <input type="number" name="registration_fee" value="{{ $settings['registration_fee'] ?? '0' }}" min="0" step="1" placeholder="e.g. 500"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-200 bg-white rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">The amount shown to candidates when payment is enabled.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-800 text-xs uppercase mb-1">UPI ID for Payment <span class="text-slate-400 font-normal">(यूपीआई आईडी)</span></label>
                            <input type="text" name="upi_id" value="{{ $settings['upi_id'] ?? '' }}" placeholder="e.g. digambarsamaj@bank"
                                   class="w-full px-4 py-2 border border-gray-200 bg-white rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                            <p class="text-[11px] text-slate-500 mt-1">Displayed alongside QR code for direct UPI transfers.</p>
                        </div>
                    </div>

                    <!-- Payment QR Code Image Upload -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-3 border-t border-indigo-100">
                        <div class="flex-grow">
                            <h5 class="font-bold text-gray-800 text-sm">Payment QR Code Image</h5>
                            <p class="text-xs text-slate-500 mt-1">Upload the QR code image for payments (displayed in registration wizard and payment modal).</p>
                            @if(!empty($settings['payment_qr_code']))
                                <div class="mt-3">
                                    @if(str_starts_with($settings['payment_qr_code'], 'data:image/'))
                                        <img src="{{ $settings['payment_qr_code'] }}" alt="QR Code" class="w-24 h-24 object-cover border rounded-xl shadow-sm bg-white p-1">
                                    @else
                                        <img src="/image?file={{ urlencode(ltrim(str_replace('../', '', $settings['payment_qr_code']), '/\\')) }}" alt="QR Code" class="w-24 h-24 object-cover border rounded-xl shadow-sm bg-white p-1" onerror="this.src='https://placehold.co/200x200/fef08a/854d0e?text=QR+Code';">
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="payment_qr_code_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-indigo-700 hover:file:bg-indigo-50 cursor-pointer shadow-xs border border-gray-200 rounded-xl">
                        </div>
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
        <div id="tab-dynamic-pages" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-indigo-500"></i> Dynamic Page Contents & Policies
                </h4>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">Community Page Content (HTML Supported)</label>
                    <textarea name="community_content" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['community_content'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">About Us YouTube Video URL</label>
                    <input type="text" name="about_youtube" value="{{ $settings['about_youtube'] ?? '' }}" placeholder="https://www.youtube.com/embed/..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">This YouTube embed video is displayed on the public About Us page.</p>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">About Us Page Content (Hindi - HTML Supported)</label>
                    <textarea name="about_us" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['about_us'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 text-xs mb-1">About Us Page Content (English - HTML Supported)</label>
                    <textarea name="about_us_en" rows="5" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['about_us_en'] ?? '' }}</textarea>
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

</form>
@endsection
