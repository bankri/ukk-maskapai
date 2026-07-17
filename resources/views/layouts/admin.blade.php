<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Z-Airlines</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-100">
    <div class="min-h-screen lg:flex">
        <div id="adminBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>
        <aside id="adminSidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-slate-950 text-white flex flex-col -translate-x-full lg:translate-x-0 transition-transform">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 bg-blue-600 rounded-xl grid place-items-center"><i class="fas fa-plane-departure"></i></span>
                    <div><h1 class="text-xl font-bold">Z-Airlines</h1><p class="text-xs text-sky-300">Flight Operations</p></div>
                </div>
                <button id="closeAdminMenu" class="lg:hidden p-2" aria-label="Tutup menu"><i class="fas fa-times"></i></button>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-slate-800' }}"><i class="fas fa-gauge-high w-5"></i><span>Dashboard</span></a>
                <a href="{{ route('admin.flights') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.flights*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}"><i class="fas fa-plane-departure w-5"></i><span>Penerbangan</span></a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.bookings*') ? 'bg-blue-600' : 'hover:bg-slate-800' }}"><i class="fas fa-ticket w-5"></i><span>Booking</span></a>
            </nav>
            <div class="p-4 border-t border-slate-800 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800"><i class="fas fa-home w-5"></i><span>Website</span></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-600"><i class="fas fa-sign-out-alt w-5"></i><span>Logout</span></button></form>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
                <div class="px-4 sm:px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button id="openAdminMenu" class="lg:hidden p-2 rounded-lg border" aria-label="Buka menu"><i class="fas fa-bars"></i></button>
                        <h2 class="text-lg sm:text-xl font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:block text-sm text-slate-600">{{ auth()->user()->name }}</span>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-5">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5">{{ session('error') }}</div>@endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5"><ul class="list-disc pl-5 text-sm space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('adminBackdrop');
        const setSidebar = (open) => {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop.classList.toggle('hidden', !open);
        };
        document.getElementById('openAdminMenu')?.addEventListener('click', () => setSidebar(true));
        document.getElementById('closeAdminMenu')?.addEventListener('click', () => setSidebar(false));
        backdrop?.addEventListener('click', () => setSidebar(false));
    </script>
    @stack('scripts')
</body>
</html>
