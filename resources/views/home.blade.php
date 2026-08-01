@extends('layouts.app')

@section('title', $settings['home_title'] ?? 'Digambar Jain Matrimony')

@section('content')
<!-- Preloader -->
<div id="preloader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-500">
    <div class="flex flex-col items-center">
        <!-- Spinner -->
        <div class="relative w-16 h-16 sm:w-20 sm:h-20">
            <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-t-primary border-r-transparent border-b-transparent border-l-transparent animate-spin"></div>
            <div class="absolute inset-2 rounded-full border-4 border-gray-100"></div>
            <div class="absolute inset-2 rounded-full border-4 border-t-accent border-r-transparent border-b-transparent border-l-transparent animate-spin-reverse"></div>
        </div>
        <!-- Logo / Brand Text -->
        <div class="mt-5 flex flex-col items-center px-4 text-center">
            <h2 class="text-xl sm:text-2xl font-bold text-primary tracking-wide">Jain Digambar</h2>
            <span class="text-xs text-secondary font-semibold tracking-widest uppercase mt-1">Matrimony</span>
        </div>
    </div>
</div>

<style>
    .marquee-content {
        display: inline-block;
        padding-left: 100%;
        animation: marquee 20s linear infinite;
    }
    .marquee-content:hover {
        animation-play-state: paused;
    }
    @keyframes marquee {
        0% { transform: translate(0, 0); }
        100% { transform: translate(-100%, 0); }
    }
    @keyframes spin-reverse {
        0% { transform: rotate(360deg); }
        100% { transform: rotate(0deg); }
    }
    .animate-spin-reverse {
        animation: spin-reverse 1.2s linear infinite;
    }
</style>

<script>
    // Hide preloader immediately on DOM ready to minimize render delays
    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                if (preloader.parentNode) preloader.remove();
            }, 300);
        }
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        hidePreloader();
    } else {
        document.addEventListener('DOMContentLoaded', hidePreloader);
        window.addEventListener('load', hidePreloader);
    }
</script>

@if (!empty($marquee_ads_text))
<!-- Advertisement Marquee -->
<div class="bg-[#800000] text-white py-2.5 overflow-hidden relative shadow-inner" style="display: flex; align-items: center; white-space: nowrap;">
    <div class="marquee-content inline-block">
        <span class="text-base sm:text-lg font-bold px-4 tracking-wide">
            {!! implode(' <span class="mx-3 text-yellow-300">| ★ |</span> ', $marquee_ads_text) !!}
        </span>
    </div>
</div>
@endif

<!-- Hero Section (3-Column Layout) -->
<section class="relative flex flex-col justify-start items-center overflow-hidden bg-gray-900 pt-3 pb-6 sm:pb-8 lg:pb-10">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-primary/20 z-0"></div>

    <div class="container mx-auto px-4 relative z-20 w-full flex flex-col xl:flex-row gap-6">
        <!-- Left + Right Ads wrapper: 1-col on mobile, 2-col on tablet, becomes plain flex children (sidebars) on xl -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 xl:contents order-2 xl:order-none">

            <!-- Left Ad Panel -->
            @if (!isset($settings['show_hero_left_ad']) || $settings['show_hero_left_ad'] == '1')
            <div class="flex flex-col w-full xl:w-56 space-y-3 flex-shrink-0 xl:order-1">
                @if (!empty($left_sidebar_ads))
                    <div class="ad-rotator-container relative w-full h-full min-h-[160px] sm:min-h-[180px] xl:min-h-[200px] flex-grow rounded shadow-lg border border-gray-700 overflow-hidden bg-slate-900">
                        @foreach($left_sidebar_ads as $index => $ad)
                            @php
                                $ad_img = $ad['image'] ?? $ad['image_path'] ?? '';
                                $is_video = isset($ad['media_type']) && $ad['media_type'] === 'video';
                                $duration = isset($ad['duration_seconds']) && $ad['duration_seconds'] > 0 ? $ad['duration_seconds'] : 3;
                                
                                $ad_link = trim($ad['link'] ?? '');
                                $has_valid_link = !empty($ad_link) && $ad_link !== '#' && !str_contains(strtolower($ad_link), 'printmines');

                                if (str_starts_with($ad_img, 'data:image/')) {
                                    $img_src = $ad_img;
                                } else {
                                    $img_path = ltrim(str_replace('../', '', $ad_img), '/\\');
                                    $img_src = route('image.serve', ['file' => $img_path]);
                                }
                            @endphp
                            <div class="ad-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                                 data-duration="{{ $duration * 1000 }}">
                                @if($has_valid_link)
                                    <a href="{{ $ad_link }}" target="_blank" class="block w-full h-full">
                                        @if($is_video)
                                            <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                        @else
                                            <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                        @endif
                                    </a>
                                @else
                                    <div class="w-full h-full">
                                        @if($is_video)
                                            <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                        @else
                                            <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Unsplash Placeholder Ad -->
                    <div class="relative w-full h-full min-h-[160px] sm:min-h-[180px] xl:min-h-[200px] flex-grow rounded shadow-lg border border-gray-700 overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1583939000148-f75e1140984f?auto=format&fit=crop&w=400&q=80" alt="Advertise" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <span class="text-white font-bold text-base sm:text-lg xl:text-xl tracking-widest uppercase">Advertise</span>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Center Section (Content & Banner) -->
            <div class="flex-grow w-full col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-0 items-stretch bg-[#1a2942] rounded-2xl overflow-hidden shadow-2xl order-first xl:order-2 min-h-[220px] sm:min-h-[260px] md:min-h-[280px]" data-aos="fade-up">
                <div class="flex flex-col justify-center p-4 sm:p-5 md:p-8 lg:p-10 text-center sm:text-left h-full">
                    <h2 class="text-base sm:text-xl md:text-2xl lg:text-3xl font-bold text-white leading-tight">
                        @php
                            $hero_h = $settings['hero_heading'] ?? "The most trusted\nmatrimony\nservice for\nDigambar Jain!";
                            $hero_h = str_ireplace(['<br>', '<br/>', '<br />'], "\n", $hero_h);
                            echo nl2br(htmlspecialchars($hero_h));
                        @endphp
                    </h2>
                    <p class="hidden sm:block text-sm sm:text-base md:text-[15px] lg:text-base text-gray-300 leading-relaxed max-w-xl mt-3 sm:mt-4 md:mt-6">
                        {{ $settings['hero_description'] ?? 'This website is created only for the Digambar Jain community to help eligible young men and women of the entire Digambar Jain society find their suitable life partner.' }}
                    </p>
                </div>
                
                <div class="relative w-full h-full min-h-[140px] sm:min-h-[200px] md:min-h-[260px] flex items-center justify-center bg-[#1a2942] p-2 sm:p-4">
                    @php
                        $hero_img_src = asset('assets/images/gallery/TEMP1.jpg');
                        if (!empty($settings['hero_banner'])) {
                            if (str_starts_with($settings['hero_banner'], 'data:image/')) {
                                $hero_img_src = $settings['hero_banner'];
                            } else {
                                $clean_banner = ltrim(str_replace('../', '', $settings['hero_banner']), '/\\');
                                $hero_img_src = route('image.serve', ['file' => $clean_banner]);
                            }
                        }
                    @endphp
                    <img src="{{ $hero_img_src }}" alt="Matrimony Hero" fetchpriority="high" decoding="async" class="w-full h-full object-contain max-h-[160px] sm:max-h-[220px] md:max-h-[280px] lg:max-h-[300px]">
                </div>
            </div>

            <!-- Right Ad Panel -->
            @if (!isset($settings['show_hero_right_ad']) || $settings['show_hero_right_ad'] == '1')
            <div class="flex flex-col w-full xl:w-56 space-y-3 flex-shrink-0 xl:order-3">
                @if (!empty($right_sidebar_ads))
                    <div class="ad-rotator-container relative w-full h-full min-h-[160px] sm:min-h-[180px] xl:min-h-[200px] flex-grow rounded shadow-lg border border-gray-700 overflow-hidden bg-slate-900">
                        @foreach($right_sidebar_ads as $index => $ad)
                            @php
                                $ad_img = $ad['image'] ?? $ad['image_path'] ?? '';
                                $is_video = isset($ad['media_type']) && $ad['media_type'] === 'video';
                                $duration = isset($ad['duration_seconds']) && $ad['duration_seconds'] > 0 ? $ad['duration_seconds'] : 3;
                                
                                $ad_link = trim($ad['link'] ?? '');
                                $has_valid_link = !empty($ad_link) && $ad_link !== '#' && !str_contains(strtolower($ad_link), 'printmines');

                                if (str_starts_with($ad_img, 'data:image/')) {
                                    $img_src = $ad_img;
                                } else {
                                    $img_path = ltrim(str_replace('../', '', $ad_img), '/\\');
                                    $img_src = route('image.serve', ['file' => $img_path]);
                                }
                            @endphp
                            <div class="ad-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                                 data-duration="{{ $duration * 1000 }}">
                                @if($has_valid_link)
                                    <a href="{{ $ad_link }}" target="_blank" class="block w-full h-full">
                                        @if($is_video)
                                            <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                        @else
                                            <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                        @endif
                                    </a>
                                @else
                                    <div class="w-full h-full">
                                        @if($is_video)
                                            <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                        @else
                                            <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Unsplash Placeholder Ad -->
                    <div class="relative w-full h-full min-h-[160px] sm:min-h-[180px] xl:min-h-[200px] flex-grow rounded shadow-lg border border-gray-700 overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=400&q=80" alt="Advertise" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <span class="text-white font-bold text-base sm:text-lg xl:text-xl tracking-widest uppercase">Advertise</span>
                        </div>
                    </div>
                @endif
            </div>
            @endif

        </div>
        <!-- /Left + Right Ads wrapper -->
    </div>

    <!-- Bottom Ad Panel -->
    @if (!isset($settings['show_hero_bottom_ad']) || $settings['show_hero_bottom_ad'] == '1')
    <div class="container mx-auto px-4 relative z-20 w-full mt-3">
        <div class="flex flex-wrap justify-center gap-4 w-full">
            @if (!empty($home_bottom_ads))
                <div class="ad-rotator-container relative w-full h-[80px] sm:h-[90px] md:h-[100px] rounded shadow-lg border border-gray-700 overflow-hidden bg-slate-900">
                    @foreach($home_bottom_ads as $index => $ad)
                        @php
                            $ad_img = $ad['image'] ?? $ad['image_path'] ?? '';
                            $is_video = isset($ad['media_type']) && $ad['media_type'] === 'video';
                            $duration = isset($ad['duration_seconds']) && $ad['duration_seconds'] > 0 ? $ad['duration_seconds'] : 3;
                            
                            $ad_link = trim($ad['link'] ?? '');
                            $has_valid_link = !empty($ad_link) && $ad_link !== '#' && !str_contains(strtolower($ad_link), 'printmines');

                            if (str_starts_with($ad_img, 'data:image/')) {
                                $img_src = $ad_img;
                            } else {
                                $img_src = route('image.serve', ['file' => ltrim(str_replace('../', '', $ad_img), '/\\')]);
                            }
                        @endphp
                        <div class="ad-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                             data-duration="{{ $duration * 1000 }}">
                            @if($has_valid_link)
                                <a href="{{ $ad_link }}" target="_blank" class="block w-full h-full">
                                    @if($is_video)
                                        <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                    @else
                                        <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                    @endif
                                </a>
                            @else
                                <div class="w-full h-full">
                                    @if($is_video)
                                        <video src="{{ $img_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                    @else
                                        <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? '' }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Unsplash Placeholder Ad -->
                <div class="relative w-full h-[80px] sm:h-[90px] md:h-[100px] rounded shadow-lg border border-gray-700 overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1200&q=80" alt="Advertise" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                        <span class="text-white font-bold text-lg sm:text-xl md:text-2xl tracking-widest uppercase">Advertise</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</section>

<!-- Quick Search Section -->
<section class="bg-light relative z-20 mt-6 sm:mt-10 md:mt-16 lg:mt-24">
    <div class="container mx-auto px-4 -mt-4 sm:-mt-8 md:-mt-12 lg:-mt-16 mb-10">
        <div id="quick-search" class="bg-white bg-opacity-95 p-4 sm:p-6 rounded-xl shadow-2xl max-w-6xl mx-auto backdrop-blur-sm border-t-4 border-primary" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-lg sm:text-xl font-bold text-dark mb-4 border-b pb-2">
                <i class="fas fa-search text-primary mr-2"></i>Quick Search
            </h3>
            
            @if (!$is_logged_in)
                <div class="text-center py-6">
                    <p class="text-base sm:text-lg text-gray-700 mb-4">Please login or register to search profiles.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-primary text-white px-6 sm:px-8 py-3 rounded-md font-bold shadow-md hover:bg-opacity-90 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login to Search
                    </a>
                </div>
            @elseif (!$is_approved)
                <div class="text-center py-6">
                    <p class="text-lg sm:text-xl text-yellow-600 font-bold mb-2">
                        <i class="fas fa-clock mr-2"></i>Profile Pending Approval
                    </p>
                    <p class="text-gray-700">Your profile is pending approval. Search will be available after admin approval.</p>
                </div>
            @else
                <form action="{{ route('profiles') }}" method="GET">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Looking For -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Looking For</label>
                            <select name="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Both</option>
                                <option value="Girl">Girl (Female)</option>
                                <option value="Boy">Boy (Male)</option>
                            </select>
                        </div>
                        <!-- Age Group -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Age Group</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="age_from" placeholder="From" class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50" min="18" max="70">
                                <span class="text-gray-500 font-medium">to</span>
                                <input type="number" name="age_to" placeholder="To" class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50" min="18" max="70">
                            </div>
                        </div>
                        <!-- Marital Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Marital Status</label>
                            <select name="marital" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Any</option>
                                <option value="Never Married">Never Married</option>
                                <option value="Widow">Widow</option>
                                <option value="Widower">Widower</option>
                                <option value="Divorce">Divorcee</option>
                            </select>
                        </div>
                        <!-- Manglik -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Manglik Status</label>
                            <select name="manglik" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Any</option>
                                <option value="yes">Manglik</option>
                                <option value="no">Non-Manglik</option>
                            </select>
                        </div>
                        <!-- State -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">State</label>
                            <select name="state" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Any State</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                            </select>
                        </div>
                        <!-- City -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">City</label>
                            <input type="text" name="city" placeholder="Enter City Name" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                        </div>
                        <!-- Education -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Education</label>
                            <select name="education" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Any Education</option>
                                <option value="Bachelors">Bachelors</option>
                                <option value="Masters">Masters</option>
                                <option value="Doctors">Doctors</option>
                                <option value="Diploma">Diploma</option>
                            </select>
                        </div>
                        <!-- Profession -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Profession</label>
                            <select name="occupation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary p-2.5 border bg-gray-50">
                                <option value="">Any Profession</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Engineer">Engineer</option>
                                <option value="CA/CS">CA / CS</option>
                                <option value="Business">Business</option>
                                <option value="Service">Service</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <button type="submit" class="bg-primary text-white px-10 py-3 rounded-md text-lg font-bold hover:bg-opacity-90 transition shadow-lg w-full md:w-auto">
                            <i class="fas fa-search mr-2"></i>Search Profiles
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>

@if (isset($settings['show_matrimony_book_fee']) && $settings['show_matrimony_book_fee'] == '1')
<!-- Matrimony Book Notice Section -->
<section class="bg-yellow-50 border-y border-yellow-200 py-6 mb-12">
    <div class="container mx-auto px-4 text-center">
        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-yellow-800 mb-2">
            <i class="fas fa-book-open mr-2"></i> Free Registration
        </h3>
        <p class="text-base sm:text-lg text-yellow-700">
            If you want your photo printed in our matrimony book, a fee of Rs. 1000/- is required.
        </p>
        <p class="text-sm sm:text-md text-yellow-600 mt-2 font-medium">
            Kindly scan the QR code to pay Rs. 1000/- and mention your Mobile No. in Payment Remarks.
        </p>
        @php
            $payment_qr_code = $settings['payment_qr_code'] ?? 'assets/images/qr_code.jpg';
            $is_base64_qr = str_starts_with($payment_qr_code, 'data:image/');
            $clean_qr_code = $is_base64_qr ? '' : ltrim(str_replace('../', '', $payment_qr_code), '/\\');
            $qr_exists = $is_base64_qr || (!empty($clean_qr_code) && file_exists(public_path($clean_qr_code)));
        @endphp
        <div class="mt-4 flex justify-center">
            @if ($qr_exists)
                @if ($is_base64_qr)
                    <img src="{{ $payment_qr_code }}" alt="Payment QR" loading="lazy" decoding="async" class="w-36 h-36 sm:w-48 sm:h-48 border border-yellow-300 rounded shadow-sm object-cover">
                @else
                    <img src="{{ route('image.serve', ['file' => $clean_qr_code]) }}" alt="Payment QR" loading="lazy" decoding="async" class="w-36 h-36 sm:w-48 sm:h-48 border border-yellow-300 rounded shadow-sm object-cover">
                @endif
            @else
                <img src="https://placehold.co/200x200/fef08a/854d0e?text=QR+Code+Not+Found" alt="Payment QR Placeholder" loading="lazy" decoding="async" class="w-36 h-36 sm:w-48 sm:h-48 border border-yellow-300 rounded shadow-sm object-cover">
            @endif
        </div>
        <div class="mt-6">
            <a href="{{ route('profile.my') }}#payment-upload" class="inline-block bg-primary text-white px-6 sm:px-8 py-3 rounded-md shadow-lg hover:bg-opacity-90 transition font-bold">Upload Payment Screenshot</a>
        </div>
    </div>
</section>
@endif

@if (!empty($home_top_ads) && (isset($settings['show_home_top_ads']) ? $settings['show_home_top_ads'] == '1' : false))
<!-- Advertisements (Home Top) -->
<section class="py-8 bg-white border-b border-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-dark mb-8">Advertisements</h2>
        <div class="flex flex-wrap justify-center gap-6 items-center">
            @foreach($home_top_ads as $ad)
                @php
                    $ad_img = $ad['image'] ?? $ad['image_path'] ?? '';
                    if (str_starts_with($ad_img, 'data:image/')) {
                        $img_src = $ad_img;
                    } else {
                        $img_src = route('image.serve', ['file' => ltrim(str_replace('../', '', $ad_img), '/\\')]);
                    }
                @endphp
                <div class="block w-full max-w-[295px] aspect-[2/3] rounded-xl overflow-hidden shadow-md transition bg-white">
                    <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? 'Advertisement' }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Profiles Section -->
<section id="latest" class="py-12 sm:py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-dark mb-3 relative inline-block">Latest Profiles
                <span class="absolute bottom-0 left-1/4 w-1/2 h-1 bg-primary rounded-full -mb-2"></span>
            </h2>
            <p class="text-gray-600 mt-4">Find your life partner from our newly registered members</p>
        </div>

        <div class="flex flex-wrap justify-center mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="inline-flex rounded-md shadow-sm mb-8" role="group">
                <a href="?latest_gender=Girl#latest" class="{{ $latest_gender === 'Girl' ? 'bg-primary text-white' : 'bg-white text-dark hover:bg-gray-100 border border-r-0' }} px-4 sm:px-8 py-2.5 rounded-l-full font-bold focus:outline-none transition shadow-md text-sm sm:text-base">
                    Latest Girls
                </a>
                <a href="?latest_gender=Boy#latest" class="{{ $latest_gender === 'Boy' ? 'bg-primary text-white' : 'bg-white text-dark hover:bg-gray-100 border border-l-0' }} px-4 sm:px-8 py-2.5 rounded-r-full font-bold focus:outline-none transition shadow-md text-sm sm:text-base">
                    Latest Boys
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($index_profiles as $p)
                @php
                    $link = $is_logged_in ? route('profiles.detail', ['profile' => $p->id]) : route('login');
                @endphp
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 group border border-gray-100" data-aos="fade-up" data-aos-delay="{{ $p->delay }}">
                    <div class="relative overflow-hidden aspect-[3/4]">
                        @if ($is_approved)
                            <img src="{{ $p->computed_img }}" alt="{{ $p->full_name }}" loading="lazy" decoding="async" class="w-full h-full object-cover object-top group-hover:scale-110 transition duration-500">
                        @else
                            @php
                                $placeholder = ($p->gender == 'Female') ? asset('assets/images/bride_placeholder.png') : asset('assets/images/groom_placeholder.png');
                            @endphp
                            <div class="w-full h-full group-hover:scale-110 transition duration-500 relative">
                                <img src="{{ $placeholder }}" alt="Profile Locked" loading="lazy" decoding="async" class="w-full h-full object-cover object-top">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-30 text-white p-4 text-center z-10 backdrop-blur-[2px]">
                                    <i class="fas fa-lock text-2xl sm:text-3xl mb-2"></i>
                                </div>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black via-black/70 to-transparent p-3 sm:p-4 z-20">
                            <a href="{{ $link }}" class="text-white font-bold text-sm sm:text-lg hover:underline block truncate">{{ $p->full_name }}</a>
                            <p class="text-gray-200 text-xs sm:text-sm font-medium">{{ $p->computed_age }} Yrs, {{ $p->height ?? 'N/A' }}</p>
                        </div>
                        @if (isset($p->created_at) && $p->created_at->gt(now()->subDays(7)))
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow z-20">New</div>
                        @endif
                    </div>
                    <div class="p-3 sm:p-5">
                        <div class="space-y-2 mb-4">
                            <p class="text-xs sm:text-sm text-gray-600 flex items-center">
                                <i class="fas fa-graduation-cap w-6 text-primary mr-2"></i>
                                <span class="truncate">{{ $p->higher_education ?? 'N/A' }}</span>
                            </p>
                            <p class="text-xs sm:text-sm text-gray-600 flex items-center">
                                <i class="fas fa-briefcase w-6 text-primary mr-2"></i>
                                <span class="truncate">{{ $p->occupation ?? 'N/A' }}</span>
                            </p>
                            <p class="text-xs sm:text-sm text-gray-600 flex items-center">
                                <i class="fas fa-map-marker-alt w-6 text-primary mr-2"></i>
                                <span class="truncate">{{ $p->native_place ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <a href="{{ $link }}" class="block text-center bg-gray-50 border border-primary text-primary hover:bg-primary hover:text-white py-2 rounded-md transition font-semibold text-sm sm:text-base">
                            View Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-10">
            <a href="{{ route('profiles') }}?gender={{ urlencode($latest_gender) }}" class="inline-block bg-primary text-white px-6 sm:px-8 py-3 rounded-md shadow-lg hover:bg-opacity-90 transition font-bold text-base sm:text-lg">
                <i class="fas fa-users mr-2"></i>View All Profiles
            </a>
        </div>
    </div>
</section>

<!-- Find Matches Section -->
<section class="py-12 sm:py-16 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-dark mb-3 relative inline-block">Find Matches By Category
                <span class="absolute bottom-0 left-1/4 w-1/2 h-1 bg-primary rounded-full -mb-2"></span>
            </h2>
            <p class="text-gray-600 mt-4">Find matches based on your specific preferences</p>
        </div>

        <div class="mt-12 text-center" data-aos="fade-up">
            <a href="{{ route('profiles') }}?gender=Girl" class="inline-flex items-center justify-center bg-white border-2 border-primary text-primary px-4 sm:px-6 py-2.5 sm:py-3 rounded-md font-bold hover:bg-primary hover:text-white transition shadow-sm group mx-1 sm:mx-2 mb-2 text-sm sm:text-base">
                <i class="fas fa-female mr-2 text-primary group-hover:text-white"></i> All Girls
            </a>
            <a href="{{ route('profiles') }}?gender=Boy" class="inline-flex items-center justify-center bg-white border-2 border-primary text-primary px-4 sm:px-6 py-2.5 sm:py-3 rounded-md font-bold hover:bg-primary hover:text-white transition shadow-sm group mx-1 sm:mx-2 mb-2 text-sm sm:text-base">
                <i class="fas fa-male mr-2 text-primary group-hover:text-white"></i> All Boys
            </a>
        </div>
        <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mt-8" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('profiles') }}?education=Doctors" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-user-md mr-2 text-primary group-hover:text-white"></i> Doctors
            </a>
            <a href="{{ route('profiles') }}?education=Engineer" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-hard-hat mr-2 text-primary group-hover:text-white"></i> Engineers
            </a>
            <a href="{{ route('profiles') }}?education=MBA/MCA" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-user-graduate mr-2 text-primary group-hover:text-white"></i> MBA/MCA
            </a>
            <a href="{{ route('profiles') }}?education=CA/CS" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-calculator mr-2 text-primary group-hover:text-white"></i> CA/CS
            </a>
            <a href="{{ route('profiles') }}?occupation=Business" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-briefcase mr-2 text-primary group-hover:text-white"></i> Business
            </a>
            <a href="{{ route('profiles') }}?occupation=Service" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-laptop-house mr-2 text-primary group-hover:text-white"></i> Service
            </a>
            <a href="{{ route('profiles') }}?nri=yes" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-plane mr-2 text-primary group-hover:text-white"></i> NRI
            </a>
            <a href="{{ route('profiles') }}?manglik=yes" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-om mr-2 text-primary group-hover:text-white"></i> Manglik
            </a>
            <a href="{{ route('profiles') }}?marital=Widow" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-user-alt-slash mr-2 text-primary group-hover:text-white"></i> Widow
            </a>
            <a href="{{ route('profiles') }}?marital=Divorce" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-heart-broken mr-2 text-primary group-hover:text-white"></i> Divorcee
            </a>
            <a href="{{ route('profiles') }}?marital=Widower" class="bg-light border border-gray-200 text-dark px-4 sm:px-6 py-2.5 sm:py-3 rounded-md hover:bg-primary hover:text-white hover:border-primary transition shadow-sm font-semibold flex items-center group text-sm sm:text-base">
                <i class="fas fa-user-slash mr-2 text-primary group-hover:text-white"></i> Widower
            </a>
        </div>
    </div>
</section>

<!-- Advertisement Rotator Engine (Continuous Infinite Loop) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rotators = document.querySelectorAll('.ad-rotator-container');
    rotators.forEach(container => {
        const slides = container.querySelectorAll('.ad-slide');
        if (slides.length <= 1) return; // Only 1 ad exists in this section: stay static without rotation

        let currentIndex = 0;
        let timer = null;

        function playNextSlide(index) {
            slides.forEach((slide, idx) => {
                if (idx === index) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                    const video = slide.querySelector('video');
                    if (video) {
                        video.currentTime = 0;
                        video.play().catch(() => {});
                    }
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                    const video = slide.querySelector('video');
                    if (video) {
                        video.pause();
                    }
                }
            });

            const currentSlide = slides[index];
            const duration = parseInt(currentSlide.getAttribute('data-duration')) || 3000;

            clearTimeout(timer);
            timer = setTimeout(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                playNextSlide(currentIndex);
            }, duration);
        }

        playNextSlide(0);
    });
});
</script>
@endsection
