<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jain Digambar Matrimony - Find Your Lifepartner</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center relative z-10">
        <a href="/" class="flex items-center gap-2">
            <span class="text-2xl font-black tracking-tight text-white flex items-center gap-1.5">
                <i class="fa-solid fa-heart-pulse text-rose-500"></i>
                JDM <span class="text-rose-500 text-sm font-bold bg-rose-500/10 px-2 py-0.5 rounded-full border border-rose-500/20">Matrimony</span>
            </span>
        </a>

        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-sm font-semibold hover:text-rose-400 transition">Log In</a>
            <a href="{{ route('register') }}" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-5 rounded-full text-sm shadow-lg shadow-rose-600/30 transition">
                Register Free
            </a>
        </div>
    </header>

    <!-- Hero Content -->
    <main class="flex-grow flex items-center justify-center relative px-6 py-12">
        <!-- Glowing Ambient Backdrops -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-rose-600/10 rounded-full blur-3xl -z-10 animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl -z-10 animate-pulse delay-1000"></div>

        <div class="w-full max-w-4xl mx-auto text-center space-y-8">
            <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-tight text-white">
                Find Your Sacred Bond in <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-pink-400 to-indigo-400 font-serif italic">Digambar Jain Samaj</span>
            </h1>
            
            <p class="text-base md:text-lg text-slate-400 max-w-2xl mx-auto font-light leading-relaxed">
                Connect with verified, cultured, and educated candidates. Secured with dynamic OTP validation, Mandir trust authentications, and personalized custom search algorithms.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black px-8 py-4 rounded-full text-base shadow-xl shadow-rose-600/40 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Create Candidate Profile
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold px-8 py-4 rounded-full text-base transition flex items-center justify-center gap-2 backdrop-blur-sm">
                    <i class="fa-solid fa-right-to-bracket"></i> Candidate Dashboard
                </a>
            </div>
            
            <!-- Value Props -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-12 text-left max-w-3xl mx-auto">
                <div class="bg-slate-800/40 p-5 rounded-2xl border border-slate-800/80 backdrop-blur-sm space-y-2">
                    <i class="fa-solid fa-shield-halved text-rose-400 text-xl"></i>
                    <h3 class="font-bold text-white text-sm">Verified Credentials</h3>
                    <p class="text-xs text-slate-400 font-light">Every profile undergoes a manual Mandir verification and ID audit before matching.</p>
                </div>
                <div class="bg-slate-800/40 p-5 rounded-2xl border border-slate-800/80 backdrop-blur-sm space-y-2">
                    <i class="fa-solid fa-key text-rose-400 text-xl"></i>
                    <h3 class="font-bold text-white text-sm">Secure Logins</h3>
                    <p class="text-xs text-slate-400 font-light">Passwordless login fallbacks using clean mobile verification algorithms.</p>
                </div>
                <div class="bg-slate-800/40 p-5 rounded-2xl border border-slate-800/80 backdrop-blur-sm space-y-2">
                    <i class="fa-solid fa-magnifying-glass-chart text-rose-400 text-xl"></i>
                    <h3 class="font-bold text-white text-sm">Advanced Search</h3>
                    <p class="text-xs text-slate-400 font-light">Filter prospects by Gotras, specific degrees, sub-castes, and monthly incomes.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Area -->
    <footer class="w-full border-t border-slate-800/80 bg-slate-950/40 py-8 px-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <p>&copy; {{ date('Y') }} Jain Digambar Matrimony. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.login-form') }}" class="hover:text-rose-400 font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-[10px]"></i> Administrator Login
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
