<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Z-Airlines') - The Spirit of Indonesia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%); }
        .card-hover { transition: transform .25s ease, box-shadow .25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(15,23,42,.12); }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">
    <nav class="bg-white/95 backdrop-blur shadow-sm sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2 min-w-0">
                    <span class="w-10 h-10 rounded-xl bg-blue-600 text-white grid place-items-center shadow-sm"><i class="fas fa-plane-departure"></i></span>
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">Z-Airlines</h1>
                        <p class="text-[10px] sm:text-xs text-blue-600 -mt-1 font-semibold tracking-wide">FLY WITH CONFIDENCE</p>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-7">
                    <a href="{{ route('home') }}" class="text-slate-700 hover:text-blue-600 font-medium">Beranda</a>
                    @auth
                        <a href="{{ auth()->user()->hasVerifiedEmail() ? route('bookings.index') : route('verification.notice') }}" class="text-slate-700 hover:text-blue-600 font-medium">Booking Saya</a>
                    @endauth
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <span class="text-sm text-slate-600">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->hasVerifiedEmail() ? route('user.dashboard') : route('verification.notice')) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 font-semibold shadow-sm">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-red-600 p-2" aria-label="Logout">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 font-semibold">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 font-semibold shadow-sm">Daftar</a>
                    @endauth
                </div>

                <button id="mobileMenuButton" class="md:hidden p-2 text-slate-700" aria-label="Buka menu" aria-expanded="false">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden border-t py-4 space-y-2">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Beranda</a>
                @auth
                    <a href="{{ auth()->user()->hasVerifiedEmail() ? route('bookings.index') : route('verification.notice') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Booking Saya</a>
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->hasVerifiedEmail() ? route('user.dashboard') : route('verification.notice')) }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Masuk</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg bg-blue-600 text-white">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-5">
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="flex-1">@yield('content')</main>

    <footer class="bg-slate-950 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3"><i class="fas fa-plane-departure text-sky-400 text-2xl"></i><h3 class="text-xl font-bold">Z-Airlines</h3></div>
                    <p class="text-slate-300 text-sm">Booking penerbangan yang transparan, aman, dan mudah dipantau dari pengajuan hingga perjalanan selesai.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Layanan</h4>
                    <ul class="space-y-2 text-slate-300 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Cari Penerbangan</a></li>
                        @auth<li><a href="{{ auth()->user()->hasVerifiedEmail() ? route('bookings.index') : route('verification.notice') }}" class="hover:text-white">Riwayat Booking</a></li>@endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Keamanan</h4>
                    <p class="text-slate-300 text-sm">Pembayaran melalui Midtrans, database PostgreSQL Supabase, captcha, dan verifikasi email.</p>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-6 text-slate-400 text-sm">© 2026 Z-Airlines.</div>
        </div>
    </footer>

    <script>
        const mobileButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileButton?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileButton.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
