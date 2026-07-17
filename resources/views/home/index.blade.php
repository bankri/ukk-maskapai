@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-blue-700"></div>
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-sky-400/20 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-blue-500/20 blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-24 pb-32 sm:pb-40">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-sky-300/30 bg-white/10 px-4 py-2 text-sm text-sky-100">
                <i class="fas fa-plane-departure"></i> Selamat datang di Z-Airlines
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mt-6">
                Perjalanan lebih mudah, dari booking hingga tiba di tujuan.
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 mt-6 max-w-2xl leading-relaxed">
                Cari jadwal terbaik, ajukan booking, bayar aman melalui Midtrans, dan pantau seluruh riwayat perjalanan dalam satu tempat.
            </p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="#cari-penerbangan" class="bg-sky-400 text-slate-950 px-6 py-3 rounded-xl font-bold hover:bg-sky-300 shadow-lg shadow-sky-500/20">Cari Penerbangan</a>
                @guest
                    <a href="{{ route('register') }}" class="border border-white/30 bg-white/10 px-6 py-3 rounded-xl font-bold hover:bg-white/20">Buat Akun</a>
                @else
                    <a href="{{ auth()->user()->hasVerifiedEmail() ? route('bookings.index') : route('verification.notice') }}" class="border border-white/30 bg-white/10 px-6 py-3 rounded-xl font-bold hover:bg-white/20">Lihat Booking Saya</a>
                @endguest
            </div>
        </div>
    </div>
</section>

<section id="cari-penerbangan" class="relative -mt-20 z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-5 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <p class="text-blue-600 font-bold text-sm">PENCARIAN TIKET</p>
                    <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Mau terbang ke mana?</h2>
                </div>
                <span class="text-sm text-slate-500"><i class="fas fa-shield-halved text-green-500 mr-2"></i>Booking aman dan tercatat</span>
            </div>

            <form action="{{ route('flights.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="from" class="block text-sm font-semibold text-slate-700 mb-2">Dari</label>
                    <select id="from" name="from" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bandara</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->city }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to" class="block text-sm font-semibold text-slate-700 mb-2">Ke</label>
                    <select id="to" name="to" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tujuan</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->city }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Berangkat</label>
                    <input id="date" type="date" name="date" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                        <i class="fas fa-search"></i> Cari Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <span class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 grid place-items-center"><i class="fas fa-route"></i></span>
            <div><p class="text-2xl font-extrabold text-slate-900">{{ $completedTrips }}</p><p class="text-sm text-slate-500">Perjalanan selesai</p></div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <span class="w-12 h-12 rounded-xl bg-amber-100 text-amber-500 grid place-items-center"><i class="fas fa-star"></i></span>
            <div><p class="text-2xl font-extrabold text-slate-900">{{ $totalReviews > 0 ? number_format($averageRating, 1) : '5.0' }}/5</p><p class="text-sm text-slate-500">Rating penumpang</p></div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
            <span class="w-12 h-12 rounded-xl bg-green-100 text-green-600 grid place-items-center"><i class="fas fa-credit-card"></i></span>
            <div><p class="text-lg font-extrabold text-slate-900">Midtrans</p><p class="text-sm text-slate-500">Pembayaran terverifikasi</p></div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-blue-600 font-bold text-sm">JADWAL TERDEKAT</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Penerbangan yang tersedia</h2>
            <p class="text-slate-500 mt-3">Pilih rute, lihat detail, lalu ajukan booking hingga lima penumpang.</p>
        </div>
        <a href="{{ route('flights.search') }}" class="text-blue-600 font-bold hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($flights as $flight)
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-slate-900 to-blue-800 text-white p-5">
                    <div class="flex justify-between items-center gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-xl bg-white/10 grid place-items-center"><i class="fas fa-plane"></i></span>
                            <div><p class="font-bold">{{ $flight->airline->name }}</p><p class="text-xs text-slate-300">{{ $flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                        </div>
                        <span class="text-xs rounded-full bg-green-400/20 text-green-200 px-3 py-1">{{ $flight->available_seats }} kursi</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                        <div><p class="text-2xl font-extrabold text-slate-900">{{ $flight->departureAirport->iata_code }}</p><p class="text-sm text-slate-500">{{ $flight->departureAirport->city }}</p></div>
                        <div class="text-blue-500 text-center"><i class="fas fa-plane text-lg"></i><div class="w-16 border-t border-dashed border-blue-300 mt-1"></div></div>
                        <div class="text-right"><p class="text-2xl font-extrabold text-slate-900">{{ $flight->arrivalAirport->iata_code }}</p><p class="text-sm text-slate-500">{{ $flight->arrivalAirport->city }}</p></div>
                    </div>
                    <div class="border-t border-slate-100 mt-5 pt-5 flex items-end justify-between gap-4">
                        <div><p class="text-xs text-slate-500">Mulai dari</p><p class="text-xl font-extrabold text-blue-600">Rp {{ number_format($flight->price, 0, ',', '.') }}</p><p class="text-xs text-slate-400">per penumpang</p></div>
                        <a href="{{ route('flights.show', $flight->id) }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700">Detail</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-slate-100 p-12 text-center">
                <i class="fas fa-plane-slash text-slate-300 text-5xl mb-4"></i>
                <p class="text-slate-500">Belum ada penerbangan mendatang yang tersedia.</p>
            </div>
        @endforelse
    </div>
</section>

<section class="bg-white border-y border-slate-100 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <p class="text-blue-600 font-bold text-sm">ALUR YANG JELAS</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Terbang dalam empat langkah</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'fa-magnifying-glass', 'title' => 'Cari Jadwal', 'text' => 'Pilih bandara asal, tujuan, dan tanggal keberangkatan.'],
                ['icon' => 'fa-ticket', 'title' => 'Ajukan Booking', 'text' => 'Isi identitas, data penumpang, dan nomor kursi.'],
                ['icon' => 'fa-user-check', 'title' => 'Konfirmasi Admin', 'text' => 'Admin memeriksa request sebelum pembayaran dibuka.'],
                ['icon' => 'fa-plane-arrival', 'title' => 'Bayar & Terbang', 'text' => 'Bayar melalui Midtrans, pantau history, lalu beri rating.'],
            ] as $index => $step)
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                    <div class="flex items-center justify-between mb-5"><span class="w-12 h-12 rounded-xl bg-blue-600 text-white grid place-items-center"><i class="fas {{ $step['icon'] }}"></i></span><span class="text-3xl font-extrabold text-slate-200">0{{ $index + 1 }}</span></div>
                    <h3 class="font-extrabold text-slate-900 text-lg">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-blue-600 font-bold text-sm">CERITA PENUMPANG</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Pengalaman setelah perjalanan</h2>
            <p class="text-slate-500 mt-3">Rating hanya dapat diberikan oleh user dengan booking terbayar dan telah selesai.</p>
        </div>
        @if($totalReviews > 0)
            <div class="rounded-2xl bg-amber-50 border border-amber-100 px-5 py-3"><span class="text-amber-500">★★★★★</span><strong class="ml-2">{{ number_format($averageRating, 1) }}</strong><span class="text-slate-500 text-sm"> dari {{ $totalReviews }} ulasan</span></div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($reviews as $review)
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="text-amber-400 tracking-wide mb-4">
                    @for($star = 1; $star <= 5; $star++)<i class="{{ $star <= $review->rating ? 'fas' : 'far' }} fa-star"></i>@endfor
                </div>
                <p class="text-slate-700 leading-relaxed">“{{ $review->comment }}”</p>
                <div class="border-t border-slate-100 mt-5 pt-4 flex items-center justify-between gap-4">
                    <div><p class="font-bold text-slate-900">{{ \Illuminate\Support\Str::before($review->user->name, ' ') }}</p><p class="text-xs text-slate-500">Penumpang terverifikasi</p></div>
                    <p class="text-xs text-right text-slate-500">{{ $review->booking->flight->departureAirport->iata_code }} → {{ $review->booking->flight->arrivalAirport->iata_code }}</p>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-2xl bg-blue-50 border border-blue-100 p-10 text-center"><i class="fas fa-star text-blue-300 text-4xl mb-4"></i><p class="text-blue-800 font-semibold">Rating perjalanan akan tampil di sini setelah booking selesai.</p></div>
        @endforelse
    </div>
</section>
@endsection
