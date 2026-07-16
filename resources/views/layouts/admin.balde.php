<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Z-Airlines</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white flex flex-col">
            <div class="p-6 border-b border-blue-800">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-plane text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold">Z-Airlines</h1>
                        <p class="text-xs text-blue-300">Admin Panel</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.flights') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.flights*') ? 'bg-blue-800' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-plane w-5"></i>
                    <span>Penerbangan</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.bookings*') ? 'bg-blue-800' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-ticket-alt w-5"></i>
                    <span>Booking</span>
                </a>
            </nav>
            <div class="p-4 border-t border-blue-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-blue-800">
                    <i class="fas fa-home w-5"></i>
                    <span>Kembali ke Website</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-red-600 mt-2">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>