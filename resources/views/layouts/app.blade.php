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
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <i class="fas fa-plane text-blue-600 text-2xl"></i>
                        <div>
                            <h1 class="text-xl font-bold text-blue-900">Z-Airlines</h1>
                            <p class="text-xs text-gray-500 -mt-1">THE SPIRIT OF INDONESIA</p>
                        </div>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Destinasi</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Tentang Kami</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Dashboard
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-700">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-blue-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-plane text-2xl"></i>
                        <h3 class="text-xl font-bold">Z-Airlines</h3>
                    </div>
                    <p class="text-blue-200 text-sm">Terbang lebih jauh bersama kami. Pengalaman terbang khas Indonesia dengan pelayanan terbaik dan harga terjangkau.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white">Karir</a></li>
                        <li><a href="#" class="hover:text-white">Berita Terbaru</a></li>
                        <li><a href="#" class="hover:text-white">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Layanan Pelanggan</h4>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        <li><a href="#" class="hover:text-white">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white">Informasi Bagasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        <li><i class="fas fa-phone mr-2"></i>0800-123-4567 (Gratis)</li>
                        <li><i class="fas fa-envelope mr-2"></i>support@zairlines.com</li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-blue-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-blue-200 text-sm">© 2026 Z-Airlines. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>