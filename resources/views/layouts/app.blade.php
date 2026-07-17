<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Z-Airlines') - The Spirit of Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-blue { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2 min-w-0">
                    <i class="fas fa-plane text-blue-600 text-2xl"></i>
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-blue-900 truncate">Z-Airlines</h1>
                        <p class="text-[10px] sm:text-xs text-gray-500 -mt-1">THE SPIRIT OF INDONESIA</p>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-7">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                    @auth
                        <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Booking Saya</a>
                    @endauth
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <span class="text-sm text-gray-600">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 p-2" aria-label="Logout">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 font-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">Daftar</a>
                    @endauth
                </div>

                <button id="mobileMenuButton" class="md:hidden p-2 text-gray-700" aria-label="Buka menu" aria-expanded="false">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden border-t py-4 space-y-2">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Beranda</a>
                @auth
                    <a href="{{ route('bookings.index') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Booking Saya</a>
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50">Dashboard</a>
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
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-blue-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-plane text-2xl"></i>
                        <h3 class="text-xl font-bold">Z-Airlines</h3>
                    </div>
                    <p class="text-blue-200 text-sm">Sistem booking penerbangan dengan proses persetujuan dan pembayaran yang transparan.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Layanan</h4>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Cari Penerbangan</a></li>
                        @auth
                            <li><a href="{{ route('bookings.index') }}" class="hover:text-white">Riwayat Booking</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Keamanan</h4>
                    <p class="text-blue-200 text-sm">Pembayaran diproses melalui Midtrans. Data aplikasi tersimpan pada PostgreSQL Supabase.</p>
                </div>
            </div>
            <div class="border-t border-blue-800 mt-8 pt-6 text-blue-200 text-sm">© 2026 Z-Airlines.</div>
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
