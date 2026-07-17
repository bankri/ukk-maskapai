@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-blue-600 font-bold mb-2">RIWAYAT PERJALANAN</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Booking Saya</h1>
            <p class="text-slate-500 mt-2">Cari dan pantau approval, pembayaran, perjalanan, serta history.</p>
        </div>
        <a href="{{ route('home') }}#cari-penerbangan" class="inline-flex justify-center items-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-blue-700"><i class="fas fa-search"></i> Cari Penerbangan</a>
    </div>

    <form method="GET" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 mb-7">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label for="q" class="block text-sm font-semibold text-slate-700 mb-2">Cari Booking</label>
                <input id="q" name="q" value="{{ request('q') }}" placeholder="Kode, penumpang, kota, atau IATA" class="w-full rounded-xl border-slate-300 px-4 py-3">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status Booking</label>
                <select id="status" name="status" class="w-full rounded-xl border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    <option value="pending" @selected(request('status') === 'pending')>Menunggu Admin</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Diterima</option>
                    <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label for="payment_status" class="block text-sm font-semibold text-slate-700 mb-2">Pembayaran</label>
                <select id="payment_status" name="payment_status" class="w-full rounded-xl border-slate-300 px-4 py-3">
                    <option value="">Semua</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>Belum Bayar</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>Terbayar</option>
                    <option value="failed" @selected(request('payment_status') === 'failed')>Gagal</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="flex-1 bg-slate-900 text-white px-4 py-3 rounded-xl font-bold hover:bg-blue-700">Filter</button>
                <a href="{{ route('bookings.index') }}" class="px-4 py-3 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50" title="Reset"><i class="fas fa-rotate-left"></i></a>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-semibold text-slate-700 mb-2">Berangkat dari</label>
                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-slate-300 px-4 py-3">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-semibold text-slate-700 mb-2">Sampai</label>
                <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-slate-300 px-4 py-3">
            </div>
        </div>
    </form>

    @if($bookings->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 sm:p-14 text-center">
            <i class="fas fa-ticket text-slate-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">Booking tidak ditemukan</h3>
            <p class="text-slate-500">Ubah filter atau pilih penerbangan baru dari beranda.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($bookings as $booking)
                @php
                    $isPaid = $booking->payment?->payment_status === 'paid';
                    $primaryLabel = $booking->completed_at
                        ? 'Perjalanan Selesai'
                        : ($isPaid ? 'Terbayar' : match($booking->status) {
                            'confirmed' => 'Diterima Admin',
                            'pending' => 'Menunggu Admin',
                            default => 'Ditolak / Dibatalkan',
                        });
                    $primaryClass = $booking->completed_at
                        ? 'bg-sky-100 text-sky-700'
                        : ($isPaid ? 'bg-emerald-100 text-emerald-700' : match($booking->status) {
                            'confirmed' => 'bg-green-100 text-green-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            default => 'bg-red-100 text-red-700',
                        });
                    $paymentLabel = match($booking->payment?->payment_status) {
                        'paid' => 'Terbayar',
                        'failed' => 'Gagal',
                        'pending' => 'Belum Bayar',
                        default => 'Tidak tersedia',
                    };
                @endphp
                <article class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 sm:p-7">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                            <div>
                                <p class="text-sm text-slate-500">Kode Booking</p>
                                <p class="text-xl sm:text-2xl font-extrabold text-blue-600">{{ $booking->booking_code }}</p>
                                <p class="text-xs text-slate-400 mt-1">Diajukan {{ $booking->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $primaryClass }}">{{ $primaryLabel }}</span>
                                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $isPaid ? 'bg-emerald-50 text-emerald-700' : ($booking->payment?->payment_status === 'failed' ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-700') }}">Pembayaran: {{ $paymentLabel }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div><p class="text-sm text-slate-500">Rute</p><p class="font-semibold">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p></div>
                            <div><p class="text-sm text-slate-500">Keberangkatan</p><p class="font-semibold">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                            <div><p class="text-sm text-slate-500">Penumpang</p><p class="font-semibold">{{ $booking->total_passengers }} orang</p></div>
                            <div><p class="text-sm text-slate-500">Total</p><p class="font-extrabold text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                        </div>

                        @if($booking->rejected_reason)
                            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800"><strong>Alasan:</strong> {{ $booking->rejected_reason }}</div>
                        @endif

                        <details class="group border border-slate-200 rounded-xl">
                            <summary class="cursor-pointer list-none flex items-center justify-between p-4 font-semibold">
                                <span>Detail {{ $booking->passengers->count() }} Penumpang</span>
                                <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                            </summary>
                            <div class="border-t p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($booking->passengers as $passenger)
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <div class="flex justify-between gap-3"><strong>{{ $passenger->full_name }}</strong><span class="font-bold text-blue-600">{{ $passenger->seat_number }}</span></div>
                                        <p class="text-sm text-slate-600 mt-2">{{ $passenger->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} · {{ $passenger->birth_date->format('d M Y') }}</p>
                                        <p class="text-sm text-slate-600">{{ strtoupper($passenger->identity_type ?? 'identitas') }}: {{ $passenger->identity_number ?? $passenger->passport_number }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </details>

                        <div class="mt-6 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <p class="text-sm text-slate-500">
                                @if($booking->completed_at)
                                    Perjalanan selesai {{ $booking->completed_at->format('d M Y, H:i') }}.
                                @elseif($booking->status === 'pending')
                                    Admin perlu menerima booking sebelum pembayaran.
                                @elseif($booking->status === 'confirmed' && $booking->payment?->payment_status === 'pending')
                                    Booking diterima dan siap dibayar.
                                @elseif($isPaid)
                                    Pembayaran telah terverifikasi. Menunggu perjalanan ditandai selesai.
                                @else
                                    Booking tidak dapat diproses lebih lanjut.
                                @endif
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2">
                                @if($booking->isPayable())
                                    <a href="{{ route('payments.show', $booking) }}" class="inline-flex justify-center items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700"><i class="fas fa-credit-card"></i> Bayar dengan Midtrans</a>
                                @endif
                                @if($booking->status === 'confirmed' && $booking->payment && ! $isPaid)
                                    <form method="POST" action="{{ route('payments.sync', $booking) }}">
                                        @csrf
                                        <button class="w-full inline-flex justify-center items-center gap-2 border border-blue-200 text-blue-700 px-5 py-3 rounded-xl font-bold hover:bg-blue-50"><i class="fas fa-rotate"></i> Perbarui Status</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- FITUR REVIEW & HISTORY DITUTUP SEMENTARA AGAR TIDAK ERROR --}}
                        {{-- 
                        @if($booking->canBeReviewed())
                            <form method="POST" action="{{ route('bookings.review', $booking) }}" class="mt-6 border-t border-slate-100 pt-6">
                                @csrf
                                <div class="rounded-2xl bg-amber-50 border border-amber-100 p-5">
                                    <h3 class="font-extrabold text-slate-900"><i class="fas fa-star text-amber-400 mr-2"></i>Bagaimana pengalaman penerbangan Anda?</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-4 mt-4 items-end">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">Rating</label>
                                            <select name="rating" required class="w-full rounded-xl border-slate-300 px-4 py-3">
                                                <option value="">Pilih</option>
                                                @for($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}">{{ $rating }} / 5</option>@endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ulasan</label>
                                            <textarea name="comment" rows="2" maxlength="1000" placeholder="Ceritakan pengalaman perjalanan Anda" class="w-full rounded-xl border-slate-300 px-4 py-3"></textarea>
                                        </div>
                                        <button class="bg-amber-400 text-slate-950 px-6 py-3 rounded-xl font-extrabold hover:bg-amber-300">Kirim Rating</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        @if($booking->histories->isNotEmpty())
                            <div class="bg-slate-50 border-t px-5 sm:px-7 py-5 mt-6">
                                <p class="text-sm font-bold text-slate-800 mb-3">History</p>
                                <div class="space-y-3">
                                    @foreach($booking->histories->take(8) as $history)
                                        <div class="flex gap-3 text-sm">
                                            <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                            <div><p class="text-slate-700">{{ $history->note ?: $history->to_status }}</p><p class="text-xs text-slate-400">{{ $history->created_at->format('d M Y, H:i') }}{{ $history->actor ? ' · '.$history->actor->name : '' }}</p></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        --}}

                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection