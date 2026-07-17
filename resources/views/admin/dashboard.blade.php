@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 sm:gap-5 mb-8">
    @php
        $cards = [
            ['label' => 'Total User', 'value' => $totalUsers, 'icon' => 'fa-users', 'box' => 'bg-blue-100', 'text' => 'text-blue-600'],
            ['label' => 'Penerbangan', 'value' => $totalFlights, 'icon' => 'fa-plane', 'box' => 'bg-green-100', 'text' => 'text-green-600'],
            ['label' => 'Total Booking', 'value' => $totalBookings, 'icon' => 'fa-ticket', 'box' => 'bg-purple-100', 'text' => 'text-purple-600'],
            ['label' => 'Menunggu Review', 'value' => $pendingBookings, 'icon' => 'fa-clock', 'box' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between gap-4">
                <div><p class="text-sm text-gray-500">{{ $card['label'] }}</p><p class="text-3xl font-bold text-gray-900 mt-1">{{ $card['value'] }}</p></div>
                <div class="w-12 h-12 {{ $card['box'] }} rounded-xl flex items-center justify-center"><i class="fas {{ $card['icon'] }} {{ $card['text'] }} text-xl"></i></div>
            </div>
        </div>
    @endforeach

    <div class="bg-blue-900 text-white rounded-2xl shadow-sm p-5 sm:col-span-2 xl:col-span-1">
        <p class="text-sm text-blue-200">Pendapatan Lunas</p>
        <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-blue-300 mt-2">Hanya transaksi Midtrans berstatus paid</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b">
        <div><h2 class="text-xl font-bold text-gray-900">Booking Terbaru</h2><p class="text-sm text-gray-500 mt-1">Pantau approval dan pembayaran terakhir.</p></div>
        <a href="{{ route('admin.bookings') }}" class="text-blue-600 font-semibold hover:text-blue-700">Kelola Semua <i class="fas fa-arrow-right ml-1"></i></a>
    </div>

    <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 text-sm text-gray-500">
                <tr><th class="text-left py-3 px-5">Kode</th><th class="text-left py-3 px-5">User</th><th class="text-left py-3 px-5">Rute</th><th class="text-left py-3 px-5">Status</th><th class="text-left py-3 px-5">Pembayaran</th><th class="text-left py-3 px-5">Total</th></tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-4 px-5 font-semibold text-blue-600">{{ $booking->booking_code }}</td>
                        <td class="py-4 px-5">{{ $booking->user->name }}</td>
                        <td class="py-4 px-5">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</td>
                        <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($booking->status) }}</span></td>
                        <td class="py-4 px-5">{{ ucfirst($booking->payment?->payment_status ?? '-') }}</td>
                        <td class="py-4 px-5 font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-500">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y">
        @forelse($recentBookings as $booking)
            <div class="p-5">
                <div class="flex justify-between gap-3"><strong class="text-blue-600">{{ $booking->booking_code }}</strong><span class="text-sm">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
                <p class="font-medium mt-2">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $booking->user->name }} · {{ ucfirst($booking->status) }} · Bayar {{ ucfirst($booking->payment?->payment_status ?? '-') }}</p>
            </div>
        @empty
            <div class="p-10 text-center text-gray-500">Belum ada booking.</div>
        @endforelse
    </div>
</div>
@endsection
