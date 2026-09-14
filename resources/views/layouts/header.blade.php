<!-- layouts/header.blade.php -->

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Mobile Drawer Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="p-6 bg-gradient-to-br from-[#1E3A5F] to-[#112239] text-white relative">
        <button id="closeMobileMenu" aria-label="Close mobile menu" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition focus:outline-none">
            <i class="fas fa-times text-lg"></i>
        </button>
        <div class="flex items-center gap-3 mt-2">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 p-0.5 shadow-md flex items-center justify-center flex-shrink-0">
                <div class="w-full h-full bg-[#7A161B] rounded-full flex items-center justify-center text-amber-300 font-black text-xl">
                    卐
                </div>
            </div>
            <div>
                <h3 class="font-bold text-white text-base leading-tight">दिगम्बर जैन</h3>
                <p class="text-xs text-amber-300 font-medium">परिचय सम्मेलन समिति • Matrimony</p>
            </div>
        </div>

        @if($is_logged_in)
            <div class="mt-4 pt-3 border-t border-white/15 flex items-center gap-3">
                <img src="{{ $hdr_profile_img }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-amber-400">
                <div class="truncate">
                    <p class="text-xs text-gray-300">Welcome,</p>
                    <p class="font-bold text-sm text-white truncate">{{ $hdr_user_name }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="flex flex-col space-y-2 p-6 text-sm font-medium">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:text-primary hover:bg-gray-50' }} flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
            <i class="fas fa-home w-5 text-center text-primary"></i> Home
        </a>
        
        <div class="relative">
            <div class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl hover:bg-gray-50 text-gray-700">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') || request()->routeIs('community') || request()->routeIs('sarankshak') ? 'text-primary font-bold' : 'text-gray-700 hover:text-primary' }} flex items-center gap-3 flex-grow">
                    <i class="fas fa-landmark w-5 text-center text-primary"></i> About Us
                </a>
                <button onclick="document.getElementById('mobileAboutMenu').classList.toggle('hidden'); document.getElementById('mobileAboutIcon').classList.toggle('rotate-180');" class="focus:outline-none p-1 text-gray-500">
                    <i id="mobileAboutIcon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
            </div>
            <div id="mobileAboutMenu" class="pl-12 pr-4 space-y-1.5 hidden pb-2">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }} block py-1.5 text-xs transition">About Organization</a>
                <a href="{{ route('community') }}" class="{{ request()->routeIs('community') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }} block py-1.5 text-xs transition">Executive Committee</a>
                <a href="{{ route('sarankshak') }}" class="{{ request()->routeIs('sarankshak') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }} block py-1.5 text-xs transition">Our Sarankshak</a>
            </div>
        </div>

        <a href="{{ route('stories') }}" class="{{ request()->routeIs('stories') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:text-primary hover:bg-gray-50' }} flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
            <i class="fas fa-heart w-5 text-center text-rose-500"></i> Success Story
        </a>
        <a href="{{ route('profiles') }}" class="{{ request()->routeIs('profiles') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-300' : 'bg-amber-500/10 text-amber-900 font-semibold' }} flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
            <i class="fas fa-search-heart w-5 text-center text-amber-600"></i> Find Your Match ✨
        </a>
        <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:text-primary hover:bg-gray-50' }} flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
            <i class="fas fa-images w-5 text-center text-primary"></i> Gallery
        </a>
        <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:text-primary hover:bg-gray-50' }} flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
            <i class="fas fa-bullhorn w-5 text-center text-primary"></i> News & Updates
        </a>
        
        <div class="pt-4 mt-2 border-t border-gray-100 space-y-2">
            @if($is_logged_in)
                <a href="{{ route('profile.my') }}" class="{{ request()->routeIs('profile.my') || request()->routeIs('registration.wizard') ? 'bg-primary text-white font-bold' : 'bg-gray-100 text-dark hover:bg-primary hover:text-white' }} flex items-center justify-center gap-2 py-3 rounded-xl transition text-center shadow-sm">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();" class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-red-600 hover:bg-red-50 transition text-center font-medium">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-primary text-white hover:bg-primary-dark font-bold transition shadow-sm text-center">
                    <i class="fas fa-sign-in-alt"></i> Candidate Login
                </a>
                <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold transition text-center">
                    <i class="fas fa-user-plus"></i> Register Free
                </a>
            @endif
        </div>

        <div class="pt-4 mt-2 text-center text-xs text-gray-500">
            <p class="font-semibold text-gray-700"><i class="fas fa-phone-alt mr-1 text-primary"></i> Helpline: +91 7575005121</p>
            <p class="mt-1">अहिंसा परमो धर्मः</p>
        </div>
    </div>
</div>

<!-- Header Section (White Top Bar with Subtle Warm Gradient) -->
<header class="bg-white shadow-xs sticky top-0 z-40 transition duration-300">
    <!-- Top Micro Utility Bar (Navy with Gold Accents) -->
    <div class="bg-gradient-to-r from-[#0F1D30] via-[#1E3A5F] to-[#0F1D30] text-gray-200 text-xs py-1.5 border-b border-amber-500/20">
        <div class="container mx-auto px-4 md:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="tel:+917575005121" class="hover:text-amber-300 transition flex items-center gap-1.5 font-medium">
                    <i class="fas fa-phone-alt text-amber-400 text-[11px]"></i> +91 7575005121
                </a>
                <a href="https://wa.me/917575005121" target="_blank" class="hover:text-amber-300 transition hidden sm:flex items-center gap-1.5 font-medium">
                    <i class="fab fa-whatsapp text-emerald-400 text-[12px]"></i> WhatsApp Support
                </a>
            </div>
            <div class="flex items-center space-x-3 text-[11px] text-amber-200/90 font-medium">
                <span class="flex items-center gap-1"><i class="fas fa-shield-alt text-amber-400"></i> 100% Verified Community Profiles</span>
            </div>
        </div>
    </div>

    <!-- Main Branding Header Banner (Traditional Digambar Jain Theme) -->
    <div class="bg-gradient-to-b from-[#FFFDF9] via-[#FAF6EE] to-[#F5EFE1] border-b border-amber-900/10 py-3 md:py-4 relative overflow-hidden">
        <!-- Traditional dot background pattern -->
        <div class="absolute inset-0 opacity-[0.035] pointer-events-none" style="background-image: radial-gradient(#7A161B 1px, transparent 1px); background-size: 16px 16px;"></div>

        <div class="container mx-auto px-4 md:px-8 relative z-10 flex items-center justify-between">
            
            <!-- Center: Title & Tagline -->
            <div data-aos="fade-down" class="flex-grow text-center px-2">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center group">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-[40px] font-extrabold text-[#7A161B] tracking-wide leading-tight group-hover:text-[#9B2226] transition drop-shadow-sm font-serif">
                        दिगम्बर जैन परिचय सम्मेलन समिति
                    </h1>
                    <div class="mt-1.5 inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200/80 shadow-xs">
                        <span class="text-amber-600 text-xs">✨</span>
                        <span class="text-xs sm:text-sm font-semibold text-[#8B1E22] tracking-wide">
                            दिगम्बर जैन समाज के विवाह योग्य युवक-युवतियों के जीवनसाथी चयन में सहायक एकमात्र वेबसाईट
                        </span>
                        <span class="text-amber-600 text-xs">✨</span>
                    </div>
                </a>
            </div>

            <!-- Right: Quick Action / Profile Snippet (Desktop) -->
            <div class="hidden lg:flex lg:absolute lg:right-4 md:right-8 items-center gap-3">
                @if($is_logged_in)
                    <a href="{{ route('profile.my') }}" class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-amber-400 transition group">
                        <img src="{{ $hdr_profile_img }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-amber-400">
                        <div class="text-left text-xs">
                            <span class="text-gray-500 block text-[10px]">Candidate</span>
                            <span class="font-bold text-dark group-hover:text-primary transition truncate max-w-[100px] block">{{ $hdr_user_name }}</span>
                        </div>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <i class="fas fa-user-plus text-xs"></i> Register Free
                    </a>
                @endif
            </div>

            <!-- Hamburger Icon (Mobile) -->
            <div class="md:hidden flex items-center ml-2">
                <button class="hamburger p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-dark transition focus:outline-none" id="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Section (Deep Royal Navy Navbar with Golden Accents) -->
    <nav class="bg-gradient-to-r from-[#112239] via-[#1E3A5F] to-[#112239] text-white py-2.5 hidden md:block shadow-lg border-t border-amber-400/20 border-b border-amber-400/20">
        <div class="container mx-auto px-4 md:px-8 flex justify-center items-center space-x-4 lg:space-x-7 text-[13.5px] lg:text-sm font-medium">
            
            <!-- Home -->
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-amber-300 font-bold bg-white/10 shadow-xs' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                <i class="fas fa-home text-xs {{ request()->routeIs('home') ? 'text-amber-300' : 'text-gray-400' }}"></i> Home
            </a>

            <!-- About Us Dropdown -->
            <div class="relative group">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') || request()->routeIs('community') || request()->routeIs('sarankshak') ? 'text-amber-300 font-bold bg-white/10' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <i class="fas fa-landmark text-xs {{ request()->routeIs('about') || request()->routeIs('community') || request()->routeIs('sarankshak') ? 'text-amber-300' : 'text-gray-400' }}"></i> About Us 
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180 opacity-70"></i>
                </a>
                <div class="absolute left-0 top-full mt-1.5 w-52 bg-white border border-gray-100 shadow-2xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden text-gray-800">
                    <div class="p-1.5">
                        <a href="{{ route('about') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-lg {{ request()->routeIs('about') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }} transition">
                            <i class="fas fa-info-circle text-primary w-4 text-center"></i> About Organization
                        </a>
                        <a href="{{ route('community') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-lg {{ request()->routeIs('community') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }} transition">
                            <i class="fas fa-users-cog text-primary w-4 text-center"></i> Executive Committee
                        </a>
                        <a href="{{ route('sarankshak') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-lg {{ request()->routeIs('sarankshak') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }} transition">
                            <i class="fas fa-user-shield text-primary w-4 text-center"></i> Our Sarankshak
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Stories -->
            <a href="{{ route('stories') }}" class="{{ request()->routeIs('stories') ? 'text-amber-300 font-bold bg-white/10' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                <i class="fas fa-heart text-xs text-rose-400"></i> Success Story
            </a>

            <!-- Find Your Match (Highlighted CTA Pill) -->
            <a href="{{ route('profiles') }}" class="bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400 text-slate-950 font-bold px-4 py-1.5 rounded-full shadow-md hover:shadow-amber-500/40 hover:scale-105 transition flex items-center gap-1.5 transform duration-200">
                <i class="fas fa-search-heart text-xs text-slate-950"></i> Find Your Match ✨
            </a>

            <!-- Gallery -->
            <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'text-amber-300 font-bold bg-white/10' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                <i class="fas fa-images text-xs {{ request()->routeIs('gallery') ? 'text-amber-300' : 'text-gray-400' }}"></i> Gallery
            </a>

            <!-- News & Updates -->
            <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'text-amber-300 font-bold bg-white/10' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                <i class="fas fa-bullhorn text-xs {{ request()->routeIs('news') ? 'text-amber-300' : 'text-gray-400' }}"></i> News & Updates
            </a>
            
            <!-- Auth Link -->
            @if($is_logged_in)
                <div class="flex items-center gap-2 pl-2 border-l border-white/20">
                    <a href="{{ route('profile.my') }}" class="{{ request()->routeIs('profile.my') || request()->routeIs('registration.wizard') ? 'text-amber-300 font-bold' : 'text-gray-100 hover:text-amber-300' }} transition flex items-center gap-1.5">
                        <i class="fas fa-user-circle text-xs text-amber-400"></i> My Profile
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();" class="text-red-300 hover:text-red-400 transition text-xs font-semibold ml-2 hover:underline">Logout</a>
                    <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('register.otp') ? 'text-amber-300 font-bold bg-white/10' : 'text-gray-100 hover:text-amber-300 hover:bg-white/5' }} px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 ml-1">
                    <i class="fas fa-sign-in-alt text-xs text-amber-400"></i> Login / Register
                </a>
            @endif
        </div>
    </nav>

    <!-- Scrolling News (Home Page Only) -->
    @if(request()->routeIs('home') && !empty($scrolling_news))
    <div class="bg-gradient-to-r from-[#7A161B] via-[#8B1E22] to-[#7A161B] text-white text-sm sm:text-base font-semibold py-2 overflow-hidden border-b border-amber-500/20 shadow-inner">
        <div class="container mx-auto px-4 flex items-center">
            <span class="font-bold whitespace-nowrap bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 px-3 py-0.5 rounded shadow-sm text-xs uppercase tracking-wider mr-3 flex items-center gap-1">
                <i class="fas fa-bell text-[10px]"></i> Updates
            </span>
            <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();" class="flex-grow text-amber-100">
                @foreach ($scrolling_news as $s_news)
                    <span class="mx-5">
                        @if(!empty($s_news->link))
                            <a href="{{ $s_news->link }}" target="_blank" class="hover:underline text-white font-medium hover:text-amber-300 transition">
                                <i class="fas fa-newspaper text-amber-300 text-xs mr-1"></i> {{ $s_news->content }}
                            </a>
                        @else
                            <i class="fas fa-newspaper text-amber-300 text-xs mr-1"></i> {{ $s_news->content }}
                        @endif
                    </span>
                @endforeach
            </marquee>
        </div>
    </div>
    @endif

</header>
