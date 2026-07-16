@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Booking Saya</h1>

    @if($bookings->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Booking</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki pemesanan penerbangan.</p>
            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                Cari Penerbangan
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Kode Booking</p>
                        <p class="text-xl font-bold text-blue-600">{{ $booking->booking_code }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        @if($booking->status === 'confirmed') bg-green-100 text-green-700
                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Rute</p>
                        <p class="font-semibold text-gray-900">
                            {{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-900">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Penumpang</p>
                        <p class="font-semibold text-gray-900">{{ $booking->total_passengers }} Orang</p>
                    </div>
                </div>

                <div class="border-t pt-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Total Harga</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                    @if($booking->status === 'pending')
                        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                            Bayar Sekarang
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection