<!-- layouts/header.blade.php -->

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <button id="closeMobileMenu" aria-label="Close mobile menu" class="absolute top-6 right-6 text-2xl text-gray-600 hover:text-red-500 transition focus:outline-none"><i class="fas fa-times"></i></button>
    <div class="flex flex-col space-y-6 px-8 mt-4">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">Home</a>
        <div class="relative group">
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') || request()->routeIs('community') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg flex items-center gap-2">
                About Us <i class="fas fa-chevron-down text-xs"></i>
            </a>
            <div class="pl-4 mt-2 space-y-2">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }} transition block">About Us</a>
                <a href="{{ route('community') }}" class="{{ request()->routeIs('community') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }} transition block">Community</a>
            </div>
        </div>
        <a href="{{ route('stories') }}" class="{{ request()->routeIs('stories') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">Success Story</a>
        <a href="{{ route('profiles') }}" class="{{ request()->routeIs('profiles') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">Find Your Match</a>
        <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">Gallery</a>
        <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">News & Updates</a>
        
        @if($is_logged_in)
            <a href="{{ route('profile.my') }}" class="{{ request()->routeIs('profile.my') || request()->routeIs('registration.wizard') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">
                My Profile
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();" class="text-red-500 hover:text-red-700 transition text-lg font-medium">Logout</a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        @else
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') || request()->routeIs('register') ? 'text-primary font-bold' : 'text-dark hover:text-primary font-medium' }} transition text-lg">Login / Registration</a>
        @endif
    </div>
</div>

<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <!-- Scrolling News (Home Page Only) -->
    @if(request()->routeIs('home') && !empty($scrolling_news))
    <div class="bg-primary text-white text-sm py-2 overflow-hidden">
        <div class="container mx-auto px-4 flex items-center">
            <span class="font-bold whitespace-nowrap bg-secondary px-3 py-1 rounded mr-3 shadow text-xs uppercase tracking-wider">News Updates</span>
            <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();" class="flex-grow">
                @foreach ($scrolling_news as $s_news)
                    <span class="mx-4">
                        @if(!empty($s_news->link))
                            <a href="{{ $s_news->link }}" target="_blank" class="hover:underline text-white font-medium">
                                <i class="fas fa-bell text-secondary text-xs mr-1"></i> {{ $s_news->content }}
                            </a>
                        @else
                            <i class="fas fa-bell text-secondary text-xs mr-1"></i> {{ $s_news->content }}
                        @endif
                    </span>
                @endforeach
            </marquee>
        </div>
    </div>
    @endif

    <!-- Top Title & Tagline Section -->
    <div class="container mx-auto px-4 py-4 md:py-5 flex justify-between items-center md:block text-center">
        <div data-aos="fade-down" class="flex-grow">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#8B2323] tracking-wide mb-1">दिगम्बर जैन परिचय मेट्रीमोनीयल</h1>
                <span class="text-xs md:text-sm lg:text-base text-[#8B2323] font-medium hidden sm:block">दिगम्बर जैन समाज के विवाह योग्य युवक-युवतियों के जीवनसाथी चयन में सहायक एकमात्र वेबसाईट</span>
            </a>
        </div>
        
        <!-- Hamburger Icon (Mobile) -->
        <div class="md:hidden hamburger flex-shrink-0 ml-4" id="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Navigation Section -->
    <nav class="container mx-auto px-4 md:px-8 py-3 hidden md:block">
        <div class="flex justify-center items-center space-x-6 lg:space-x-8">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">Home</a>
            <div class="relative group py-1">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') || request()->routeIs('community') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1 flex items-center gap-1">
                    About Us <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                </a>
                <div class="absolute left-0 top-full w-48 bg-white border border-gray-100 shadow-xl rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden">
                    <a href="{{ route('about') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('about') ? 'bg-primary/5 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }} transition border-b border-gray-50">About Us</a>
                    <a href="{{ route('community') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('community') ? 'bg-primary/5 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }} transition">Community</a>
                </div>
            </div>
            <a href="{{ route('stories') }}" class="{{ request()->routeIs('stories') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">Success Story</a>
            <a href="{{ route('profiles') }}" class="{{ request()->routeIs('profiles') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">Find Your Match</a>
            <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">Gallery</a>
            <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">News & Updates</a>
            
            @if($is_logged_in)
                <a href="{{ route('profile.my') }}" class="{{ request()->routeIs('profile.my') || request()->routeIs('registration.wizard') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">
                    My Profile
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();" class="text-red-500 hover:text-red-700 transition font-medium ml-2">Logout</a>
                <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('register.otp') ? 'text-primary font-bold border-b-2 border-primary' : 'text-dark hover:text-primary font-medium' }} transition pb-1">Login / Registration</a>
            @endif
        </div>
    </nav>
</header>
