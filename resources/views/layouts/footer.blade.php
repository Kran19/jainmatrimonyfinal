<!-- layouts/footer.blade.php -->
<footer class="bg-gray-900 text-white pt-12 pb-6">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-lg font-bold text-accent mb-4">दिगम्बर जैन</h4>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">
                    समर्पित एवं विश्वसनीय दिगम्बर जैन वैवाहिक मंच, जो समाज के विवाह योग्य युवक-युवतियों के लिए आदर्श जीवनसाथी खोजने में सहायक है।
                </p>
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
                    <li><strong>दिगम्बर जैन परिचय सम्मेलन समिति</strong></li>
                    <li><i class="fab fa-whatsapp mr-2"></i> WhatsApp: {{ $settings['contact_phone'] ?? '+91 7575005121' }}</li>
                    <li><i class="fas fa-envelope mr-2"></i> {{ $settings['contact_email'] ?? 'info@digambarjainparichay.com' }}</li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i> {{ $settings['contact_address'] ?? 'Indore, MP' }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Jain Digambar Matrimony. All rights reserved. Established 2026.</p>
        </div>
    </div>
</footer>

<!-- Sticky WhatsApp Button -->
<a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '917575005121' }}" target="_blank" aria-label="Contact us on WhatsApp" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl hover:bg-green-600 hover:scale-110 transition-all duration-300 z-50">
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

        // Asynchronously track visitor count in background (with timestamp to prevent browser cache)
        fetch('/api/track-visit?_t=' + Date.now())
            .then(response => response.json())
            .then(data => {
                if (data.status === 'counted') {
                    console.log('Unique visit counted. Fresh visitor_count: ' + data.visitor_count);
                }
            })
            .catch(err => console.error('Error tracking visit:', err));
    });

    // Prevent Form Resubmission Warning on Refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
