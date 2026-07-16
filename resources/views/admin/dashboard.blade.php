@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total User</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Penerbangan</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalFlights }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-plane text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Booking</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalBookings }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-ticket-alt text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Booking Terbaru</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Kode</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">User</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Rute</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tanggal</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 px-4 font-semibold text-blue-600">{{ $booking->booking_code }}</td>
                    <td class="py-4 px-4">{{ $booking->user->name }}</td>
                    <td class="py-4 px-4">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</td>
                    <td class="py-4 px-4">{{ $booking->created_at->format('d M Y') }}</td>
                    <td class="py-4 px-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($booking->status === 'confirmed') bg-green-100 text-green-700
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-4 font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection