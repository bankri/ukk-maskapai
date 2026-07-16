@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mt-2">Kelola pemesanan penerbangan Anda di sini.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBookings }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Booking Dikonfirmasi</p>
                    <p class="text-3xl font-bold text-green-600">{{ $confirmedBookings }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Payment</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalBookings - $confirmedBookings }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Booking Terbaru</h2>
            <a href="{{ route('bookings.index') }}" class="text-blue-600 font-medium hover:underline">Lihat Semua</a>
        </div>

        @if($bookings->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500">Belum ada booking</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($bookings as $booking)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $booking->booking_code }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <p class="font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection