<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Sign In - Jain Digambar Matrimony</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-xl">
        <div>
            <div class="text-center">
                <i class="fa-solid fa-gopuram text-5xl text-indigo-500 mb-2"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold tracking-tight text-white">
                Admin Portal
            </h2>
            <p class="mt-2 text-center text-sm text-slate-400">
                Sign in to manage candidate profiles
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-900/50 border border-red-500 text-red-200 text-sm p-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form class="space-y-6" action="{{ route('admin.login') }}" method="POST">
            @csrf

            <div class="space-y-4 rounded-md shadow-sm">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-300 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-slate-700 bg-slate-700 placeholder-slate-400 text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-slate-700 bg-slate-700 placeholder-slate-400 text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fa-solid fa-lock text-indigo-500 group-hover:text-indigo-400"></i>
                    </span>
                    Sign in to Dashboard
                </button>
            </div>
        </form>
    </div>
</body>
</html>
