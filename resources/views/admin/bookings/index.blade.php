@extends('layouts.admin')

@section('title', 'Kelola Booking')
@section('page-title', 'Kelola Booking')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Request dan Riwayat Booking</h1>
    <p class="text-slate-500 mt-1">Cari berdasarkan kode, customer, penumpang, identitas, kota, IATA, tanggal, status, atau pembayaran.</p>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="md:col-span-2 xl:col-span-2">
            <label for="q" class="block text-sm font-semibold text-slate-700 mb-2">Pencarian</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Kode, nama, email, identitas, kota, IATA" class="w-full rounded-xl border-slate-300 px-4 py-3">
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
        <div>
            <label for="date_from" class="block text-sm font-semibold text-slate-700 mb-2">Berangkat dari</label>
            <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-slate-300 px-4 py-3">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-semibold text-slate-700 mb-2">Sampai</label>
            <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-slate-300 px-4 py-3">
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 mt-4">
        <button class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700"><i class="fas fa-filter mr-2"></i>Terapkan Filter</button>
        <a href="{{ route('admin.bookings') }}" class="border border-slate-300 text-slate-700 px-6 py-3 rounded-xl font-bold text-center hover:bg-slate-50"><i class="fas fa-rotate-left mr-2"></i>Reset</a>
        <span class="sm:ml-auto text-sm text-slate-500 self-center">{{ $bookings->total() }} booking ditemukan</span>
    </div>
</form>

<div class="space-y-5">
    @forelse($bookings as $booking)
        @php
            $isPaid = $booking->payment?->payment_status === 'paid';
            $mainLabel = $booking->completed_at ? 'Selesai' : ($isPaid ? 'Terbayar' : ucfirst($booking->status));
            $mainClass = $booking->completed_at
                ? 'bg-sky-100 text-sky-700'
                : ($isPaid ? 'bg-emerald-100 text-emerald-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')));
            $paymentLabel = match($booking->payment?->payment_status) {
                'paid' => 'Terbayar',
                'failed' => 'Gagal',
                'pending' => 'Belum Bayar',
                default => '-',
            };
        @endphp
        <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-extrabold text-blue-700">{{ $booking->booking_code }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $mainClass }}">{{ $mainLabel }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isPaid ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">Pembayaran: {{ $paymentLabel }}</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-2">{{ $booking->user->name }} · {{ $booking->user->email }}</p>
                    </div>
                    <p class="text-sm text-slate-500">Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
                    <div class="lg:col-span-2"><p class="text-xs text-slate-500">Rute</p><p class="font-semibold">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p></div>
                    <div><p class="text-xs text-slate-500">Keberangkatan</p><p class="font-semibold">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                    <div><p class="text-xs text-slate-500">Pax</p><p class="font-semibold">{{ $booking->total_passengers }}</p></div>
                    <div><p class="text-xs text-slate-500">Total</p><p class="font-extrabold text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                </div>

                <details class="group border border-slate-200 rounded-xl mb-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center p-4 font-bold"><span>Data Penumpang</span><i class="fas fa-chevron-down group-open:rotate-180 transition"></i></summary>
                    <div class="border-t p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($booking->passengers as $passenger)
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                                <div class="flex justify-between gap-3"><strong>{{ $passenger->full_name }}</strong><span class="font-bold text-blue-600">{{ $passenger->seat_number }}</span></div>
                                <p class="text-sm text-slate-600 mt-2">{{ $passenger->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} · {{ $passenger->birth_date->format('d M Y') }}</p>
                                <p class="text-sm text-slate-600 break-all">{{ strtoupper($passenger->identity_type ?? 'identitas') }}: {{ $passenger->identity_number ?? $passenger->passport_number }}</p>
                            </div>
                        @endforeach
                    </div>
                </details>

                @if($booking->review)
                    <div class="mb-6 rounded-xl border border-amber-100 bg-amber-50 p-4">
                        <div class="text-amber-400">@for($star = 1; $star <= 5; $star++)<i class="{{ $star <= $booking->review->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</div>
                        <p class="text-sm text-slate-700 mt-2"><strong>Rating user {{ $booking->review->rating }}/5.</strong> {{ $booking->review->comment }}</p>
                    </div>
                @endif

                @if($booking->status === 'pending')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 border-t pt-5">
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button class="w-full bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700"><i class="fas fa-check mr-2"></i>Terima Booking</button>
                        </form>
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <textarea name="rejected_reason" required rows="2" maxlength="1000" placeholder="Alasan penolakan" class="w-full rounded-xl border-slate-300 px-4 py-3"></textarea>
                            <button class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700"><i class="fas fa-xmark mr-2"></i>Tolak Booking</button>
                        </form>
                    </div>
                @elseif($booking->status === 'confirmed' && ! $isPaid)
                    <div class="border-t pt-5">
                        <p class="text-sm text-amber-700 font-semibold mb-3"><i class="fas fa-clock mr-2"></i>Menunggu pembayaran user.</p>
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="max-w-xl space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <textarea name="rejected_reason" required rows="2" maxlength="1000" placeholder="Alasan pembatalan sebelum pembayaran" class="w-full rounded-xl border-slate-300 px-4 py-3"></textarea>
                            <button class="bg-red-600 text-white px-5 py-3 rounded-xl font-bold">Batalkan Booking</button>
                        </form>
                    </div>
                @elseif($isPaid && ! $booking->completed_at)
                    <div class="border-t pt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-sm text-emerald-700 font-bold"><i class="fas fa-circle-check mr-2"></i>Pembayaran Terbayar dan terverifikasi.</p>
                        <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                            @csrf
                            @method('PUT')
                            <button class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700"><i class="fas fa-flag-checkered mr-2"></i>Tandai Perjalanan Selesai</button>
                        </form>
                    </div>
                @elseif($booking->completed_at)
                    <div class="border-t pt-5 text-sm text-sky-700 font-bold"><i class="fas fa-plane-arrival mr-2"></i>Perjalanan selesai {{ $booking->completed_at->format('d M Y, H:i') }}.</div>
                @endif
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl p-10 text-center text-slate-500">Tidak ada booking yang cocok dengan filter.</div>
    @endforelse
</div>

@if($bookings->hasPages())<div class="mt-8">{{ $bookings->links() }}</div>@endif
@endsection
