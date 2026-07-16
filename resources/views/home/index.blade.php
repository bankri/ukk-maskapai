@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section with Search -->
<div class="gradient-blue py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-4xl mx-auto -mb-32">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-search text-blue-600 mr-3"></i>
                Cari Penerbangan Terbaik
            </h2>
            <form action="{{ route('flights.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                    <select name="from" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ke</label>
                    <select name="to" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berangkat</label>
                    <input type="date" name="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Cari Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Destinations Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-40">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900">Terbang Lebih Jauh Bersama Mitra Maskapai Kami</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Nikmati penawaran spesial ke berbagai destinasi menarik di seluruh dunia dengan harga terbaik</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($flights as $flight)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="h-48 bg-blue-100 flex items-center justify-center relative">
                <i class="fas fa-plane text-blue-300 text-6xl"></i>
                <span class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    Perjalanan Spesial
                </span>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900">{{ $flight->arrivalAirport->city }}</h3>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $flight->departureAirport->name }} ({{ $flight->departureAirport->iata_code }}) - 
                    {{ $flight->arrivalAirport->name }} ({{ $flight->arrivalAirport->iata_code }})
                </p>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">MULAI DARI</p>
                        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($flight->price, 0, ',', '.') }}*</p>
                    </div>
                    <a href="{{ route('flights.show', $flight->id) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                        Pesan
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <i class="fas fa-plane-slash text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500">Belum ada penerbangan tersedia</p>
        </div>
        @endforelse
    </div>

    <div class="text-center mt-12">
        <a href="#" class="text-blue-600 font-semibold hover:underline inline-flex items-center">
            Lihat Semua Destinasi <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>
@endsection