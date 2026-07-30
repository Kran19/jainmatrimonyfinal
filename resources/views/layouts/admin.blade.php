<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Admin Dashboard - Jain Digambar Matrimony')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#a78bfa', // softer purple
                        secondary: '#f472b6', // softer pink
                        dark: '#1e293b',
                        sidebar: '#fff0f5', // lavender blush for cuteness
                        accent: '#c084fc'
                    },
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'], // rounded cute font
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Custom scrollbar to make it look cute and less obtrusive */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-pink-50 text-slate-700 antialiased min-h-screen flex overflow-hidden">

    <!-- Admin Success/Error Notifications -->
    @if(session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    
    @if(session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}'
                });
            });
        </script>
    @endif

    <!-- Sidebar Wrapper -->
    <aside class="w-64 bg-sidebar text-slate-700 flex-shrink-0 flex flex-col h-screen border-r border-pink-100 shadow-sm">
        <!-- Logo Branding -->
        <div class="h-20 flex items-center px-6 py-4 border-b border-pink-200 bg-white/75 shadow-xs mb-2">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-primary tracking-wide flex items-center gap-3 py-1.5 transition hover:opacity-90">
                <i class="fa-solid fa-gopuram text-2xl text-secondary drop-shadow-xs"></i>
                <span class="text-xl font-black tracking-wider text-primary uppercase">JDM Admin Panel</span>
            </a>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex-grow py-6 px-4 space-y-2 overflow-y-auto custom-scrollbar">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Route::is('admin.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-chart-line mr-3 w-5 text-center"></i>Dashboard
            </a>
            <a href="{{ route('admin.account-approvals.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/account-approvals*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-user-plus mr-3 w-5 text-center"></i>Account Approvals (Stage 1)
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/approvals*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-stamp mr-3 w-5 text-center"></i>Profile Approvals (Stage 2)
            </a>
            <a href="{{ route('admin.members.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/members') && !Request::has('status') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-users mr-3 w-5 text-center"></i>All Members
            </a>
            <a href="{{ route('admin.members.index', ['status' => 'approved']) }}" class="flex items-center pl-8 pr-4 py-1.5 rounded-lg text-xs font-bold hover:bg-white hover:text-secondary transition duration-150 {{ Request::input('status') === 'approved' ? 'bg-primary/80 text-white' : 'text-slate-500' }}">
                <i class="fa-solid fa-user-check mr-2 w-4 text-center"></i>Approved Members
            </a>
            <a href="{{ route('admin.members.index', ['status' => 'blocked']) }}" class="flex items-center pl-8 pr-4 py-1.5 rounded-lg text-xs font-bold hover:bg-white hover:text-secondary transition duration-150 {{ Request::input('status') === 'blocked' ? 'bg-primary/80 text-white' : 'text-slate-500' }}">
                <i class="fa-solid fa-user-xmark mr-2 w-4 text-center"></i>Blocked Members
            </a>
            <a href="{{ route('admin.members.index', ['status' => 'paid']) }}" class="flex items-center pl-8 pr-4 py-1.5 rounded-lg text-xs font-bold hover:bg-white hover:text-secondary transition duration-150 {{ Request::input('status') === 'paid' ? 'bg-primary/80 text-white' : 'text-slate-500' }}">
                <i class="fa-solid fa-user-shield mr-2 w-4 text-center"></i>Paid Members
            </a>
            <a href="{{ route('admin.members.index', ['status' => 'rejected']) }}" class="flex items-center pl-8 pr-4 py-1.5 rounded-lg text-xs font-bold hover:bg-white hover:text-secondary transition duration-150 {{ Request::input('status') === 'rejected' ? 'bg-primary/80 text-white' : 'text-slate-500' }}">
                <i class="fa-solid fa-user-minus mr-2 w-4 text-center"></i>Rejected Members
            </a>
            <a href="{{ route('admin.members.incomplete') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/members-incomplete*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-user-slash mr-3 w-5 text-center"></i>Incomplete Registrations
            </a>
            <a href="{{ route('admin.members.requests') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/members-requests*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-user-minus mr-3 w-5 text-center"></i>Deactivation / Deletion Requests
            </a>
            <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/payments*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-receipt mr-3 w-5 text-center"></i>Payments & Billing
            </a>
            <a href="{{ route('admin.bulk-email.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/bulk-email*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-envelope-open-text mr-3 w-5 text-center"></i>Bulk Email Send
            </a>
            <a href="{{ route('admin.bulk-whatsapp.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/bulk-whatsapp*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-brands fa-whatsapp mr-3 w-5 text-center"></i>Bulk WhatsApp Send
            </a>
            <a href="{{ route('admin.membership-plans.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/membership-plans*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-tags mr-3 w-5 text-center"></i>Membership Packages
            </a>
            <a href="{{ route('admin.registration-fields.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/registration-fields*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-sliders mr-3 w-5 text-center"></i>Registration Fields
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/reports*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-chart-pie mr-3 w-5 text-center"></i>Reports & Audits
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/contacts*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-message mr-3 w-5 text-center"></i>Contact Messages
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/settings*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-gears mr-3 w-5 text-center"></i>Settings Configuration
            </a>
            <div class="pt-4 pb-2 text-xs font-bold text-accent uppercase tracking-wider px-4">CMS Modules</div>
            <a href="{{ route('admin.cms.news.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/cms/news*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-newspaper mr-3 w-5 text-center"></i>News & Notice
            </a>
            <a href="{{ route('admin.cms.gallery.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/cms/gallery*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-images mr-3 w-5 text-center"></i>Gallery Items
            </a>
            <a href="{{ route('admin.cms.stories.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/cms/stories*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-quote-left mr-3 w-5 text-center"></i>Success Stories
            </a>
            <a href="{{ route('admin.cms.ads.index') }}" class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-white hover:text-secondary hover:shadow-sm transition duration-150 {{ Request::is('admin/cms/ads*') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-600' }}">
                <i class="fa-solid fa-rectangle-ad mr-3 w-5 text-center"></i>Advertisements
            </a>
        </nav>
        
        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-pink-200 bg-white/50">
            <div class="flex items-center mb-3 px-2">
                <i class="fa-solid fa-circle-user text-2xl text-secondary mr-3"></i>
                <div class="text-sm font-bold text-slate-700 truncate">{{ Auth::guard('admin')->user()->name }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-pink-100 hover:bg-secondary text-secondary hover:text-white rounded-xl text-sm font-bold transition duration-150 shadow-sm hover:shadow-md hover:shadow-secondary/30">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-grow flex flex-col h-screen overflow-hidden">
        <!-- Main Inner Content -->
        <div class="flex-grow p-8 overflow-y-auto">
            @yield('content')
        </div>
    </div>

</body>
</html>
