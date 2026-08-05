<!-- layouts/footer.blade.php -->
@php
    $contact_phone = $settings['contact_phone'] ?? '+91 7575005121';
    $contact_email = $settings['contact_email'] ?? 'digambarjainparichay@gmail.com';
    $contact_address = $settings['contact_address'] ?? '23-A, Shubhlaxmi Palace, Opp. Money Plant Junction, Bhuyangdev Cross Road, Sola Road, Ahmedabad-380061.';
    $whatsapp_number = preg_replace('/[^0-9]/', '', $contact_phone);
@endphp

<!-- Footer Ads -->
@if(!empty($footer_ads))
<section class="container mx-auto px-4 md:px-8 mt-12 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ count($footer_ads) > 4 ? 4 : (count($footer_ads) > 0 ? count($footer_ads) : 1) }} gap-4">
        @foreach($footer_ads as $ad)
            @php
                $ad_img = $ad['image'] ?? $ad['image_path'] ?? '';
                if (str_starts_with($ad_img, 'data:image/')) {
                    $img_src = $ad_img;
                } else {
                    $img_src = route('image.serve', ['file' => ltrim(str_replace('../', '', $ad_img), '/\\')]);
                }
            @endphp
            <div class="w-full flex justify-center items-center">
                @if(!empty($ad['link']))
                    <a href="{{ $ad['link'] }}" target="_blank" class="block w-full hover:opacity-90 transition">
                        <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? 'Advertisement' }}" loading="lazy" decoding="async" class="w-full h-auto object-contain max-h-48 rounded shadow-md border border-gray-200">
                    </a>
                @else
                    <img src="{{ $img_src }}" alt="{{ $ad['title'] ?? 'Advertisement' }}" loading="lazy" decoding="async" class="w-full h-auto object-contain max-h-48 rounded shadow-md border border-gray-200">
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- Footer -->
<footer class="bg-dark text-white">
    <div class="container mx-auto px-4 md:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold text-accent mb-4">दिगम्बर जैन परिचय मेट्रीमोनीयल</h3>
                <p class="text-gray-300 mb-6">दिगम्बर जैन समाज के विवाह योग्य युवक-युवतियों के जीवनसाथी चयन में सहायक एकमात्र वेबसाईट</p>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/profile.php?id=61591451942555" target="_blank" aria-label="Facebook Page" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/digambarjainparichay/?hl=en" target="_blank" aria-label="Instagram Profile" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-pink-600 transition text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@DigambarJainParichaySammelan20" target="_blank" aria-label="YouTube Channel" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-red-600 transition text-white">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-accent transition">About Us</a></li>
                    <li><a href="{{ route('stories') }}" class="text-gray-300 hover:text-accent transition">Success Stories</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Support</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('contact.show') }}" class="text-gray-300 hover:text-accent transition">Contact Us</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-gray-300 hover:text-accent transition">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-gray-300 hover:text-accent transition">Terms & Conditions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                <ul class="space-y-2 text-gray-300">
                    <li><strong>दिगम्बर जैन परिचय मेट्रीमोनीयल</strong></li>
                    <li><i class="fab fa-whatsapp mr-2"></i> WhatsApp: {{ $contact_phone }}</li>
                    <li><i class="fas fa-envelope mr-2"></i> {{ $contact_email }}</li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i> {{ $contact_address }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Jain Digambar Matrimony. All rights reserved. Established 2026.</p>
        </div>
    </div>
</footer>

<!-- Sticky WhatsApp Button -->
<a href="https://wa.me/{{ $whatsapp_number }}" target="_blank" aria-label="Contact us on WhatsApp" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl hover:bg-green-600 hover:scale-110 transition-all duration-300 z-50">
    <i class="fab fa-whatsapp text-3xl"></i>
</a>

<!-- Scripts (Deferred for Performance Optimization) -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js" defer></script>
@if(request()->routeIs('user.detail') || request()->routeIs('profile.*') || isset($include_pdf_js))
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" defer></script>
@endif

<!-- Main App Scripts -->
<script>
    // Counter function
    function startCounters() {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.innerText = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
    }
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Initialize Typed.js
        if(document.getElementById('typed-text')) {
            new Typed('#typed-text', {
                strings: ['Find Your Perfect Life Partner', 'Within Digambar Jain Samaj', 'Trusted Since 2026'],
                typeSpeed: 50,
                backSpeed: 30,
                loop: true
            });
        }
        
        // Initialize General Swiper (e.g. for Profiles)
        if(document.querySelector('.swiper:not(.hero-ad-swiper)')) {
            new Swiper('.swiper:not(.hero-ad-swiper)', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 3000,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }
            });
        }
        
        // Initialize Hero Ad Swiper
        if(document.querySelector('.hero-ad-swiper')) {
            new Swiper('.hero-ad-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
        
        // Start counters when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    startCounters();
                    observer.disconnect();
                }
            });
        });
        
        const counterSection = document.querySelector('.stats-section');
        if(counterSection) observer.observe(counterSection);
        
        // Hamburger Menu Toggles
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('overlay');
        
        if(hamburger) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                mobileMenu.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
            });
        }
        
        if(overlay) {
            overlay.addEventListener('click', () => {
                if (hamburger) hamburger.classList.remove('active');
                if (mobileMenu) mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        
        const closeBtn = document.getElementById('closeMobileMenu');
        if(closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (hamburger) hamburger.classList.remove('active');
                if (mobileMenu) mobileMenu.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Initialize Fancybox if library loaded
        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind("[data-fancybox]", {});
        }
    });

    // Prevent Form Resubmission Warning on Refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
