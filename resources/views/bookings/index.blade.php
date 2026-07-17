@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-blue-600 font-semibold mb-2">Riwayat tersimpan</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Booking Saya</h1>
            <p class="text-gray-500 mt-2">Pantau persetujuan admin, pembayaran, penumpang, dan kursi.</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex justify-center items-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-blue-700"><i class="fas fa-search"></i>Cari Penerbangan</a>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-10 sm:p-14 text-center">
            <i class="fas fa-ticket text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Booking</h3>
            <p class="text-gray-500">Pilih penerbangan untuk mulai membuat booking.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($bookings as $booking)
                @php
                    $statusClass = match($booking->status) {
                        'confirmed' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-amber-100 text-amber-700',
                        default => 'bg-red-100 text-red-700',
                    };
                    $statusLabel = match($booking->status) {
                        'confirmed' => 'Diterima Admin',
                        'pending' => 'Menunggu Admin',
                        default => 'Ditolak / Dibatalkan',
                    };
                @endphp
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-5 sm:p-7">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                            <div>
                                <p class="text-sm text-gray-500">Kode Booking</p>
                                <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $booking->booking_code }}</p>
                                <p class="text-xs text-gray-400 mt-1">Diajukan {{ $booking->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $booking->payment?->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($booking->payment?->payment_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                    Pembayaran: {{ ucfirst($booking->payment?->payment_status ?? 'tidak tersedia') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div><p class="text-sm text-gray-500">Rute</p><p class="font-semibold">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p></div>
                            <div><p class="text-sm text-gray-500">Keberangkatan</p><p class="font-semibold">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                            <div><p class="text-sm text-gray-500">Penumpang</p><p class="font-semibold">{{ $booking->total_passengers }} orang</p></div>
                            <div><p class="text-sm text-gray-500">Total</p><p class="font-bold text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                        </div>

                        @if($booking->rejected_reason)
                            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800"><strong>Alasan:</strong> {{ $booking->rejected_reason }}</div>
                        @endif

                        <details class="group border border-gray-200 rounded-xl">
                            <summary class="cursor-pointer list-none flex items-center justify-between p-4 font-semibold">
                                <span>Detail {{ $booking->passengers->count() }} Penumpang</span>
                                <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                            </summary>
                            <div class="border-t p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($booking->passengers as $passenger)
                                    <div class="rounded-xl bg-gray-50 p-4">
                                        <div class="flex justify-between gap-3"><strong>{{ $passenger->full_name }}</strong><span class="font-bold text-blue-600">{{ $passenger->seat_number }}</span></div>
                                        <p class="text-sm text-gray-600 mt-2">{{ $passenger->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} · {{ $passenger->birth_date->format('d M Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ strtoupper($passenger->identity_type ?? 'identitas') }}: {{ $passenger->identity_number ?? $passenger->passport_number }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </details>

                        <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <p class="text-sm text-gray-500">
                                @if($booking->status === 'pending')
                                    Admin perlu menerima booking sebelum pembayaran.
                                @elseif($booking->status === 'confirmed' && $booking->payment?->payment_status === 'pending')
                                    Booking telah diterima dan siap dibayar.
                                @elseif($booking->payment?->payment_status === 'paid')
                                    Pembayaran telah terverifikasi oleh Midtrans.
                                @else
                                    Booking tidak dapat diproses lebih lanjut.
                                @endif
                            </p>
                            @if($booking->isPayable())
                                <a href="{{ route('payments.show', $booking) }}" class="inline-flex justify-center items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700">
                                    <i class="fas fa-credit-card"></i>Bayar dengan Midtrans
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($booking->histories->isNotEmpty())
                        <div class="bg-gray-50 border-t px-5 sm:px-7 py-5">
                            <p class="text-sm font-semibold text-gray-800 mb-3">History</p>
                            <div class="space-y-3">
                                @foreach($booking->histories->take(5) as $history)
                                    <div class="flex gap-3 text-sm">
                                        <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                        <div><p class="text-gray-700">{{ $history->note ?: $history->to_status }}</p><p class="text-xs text-gray-400">{{ $history->created_at->format('d M Y, H:i') }}{{ $history->actor ? ' · '.$history->actor->name : '' }}</p></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
