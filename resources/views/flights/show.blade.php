@extends('layouts.app')

@section('title', 'Detail Penerbangan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-8 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a> / 
        <span>Penerbangan</span> / 
        <span class="text-gray-900">{{ $flight->departureAirport->iata_code }} - {{ $flight->arrivalAirport->iata_code }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Flight Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Airline Header -->
                <div class="bg-blue-50 p-6 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                            {{ substr($flight->airline->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $flight->airline->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $flight->airline->code }}</p>
                        </div>
                    </div>
                </div>

                <!-- Flight Image -->
                <div class="h-64 bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-plane text-blue-300 text-8xl"></i>
                </div>

                <!-- Flight Info -->
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">Direct Flight</span>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">Economy Class</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ $flight->departureAirport->city }} ({{ $flight->departureAirport->iata_code }}) 
                        <i class="fas fa-arrow-right text-blue-600 mx-4"></i> 
                        {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->iata_code }})
                    </h1>

                    <div class="flex items-center space-x-2 text-yellow-500 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span class="text-gray-600 text-sm ml-2">(128 ulasan)</span>
                    </div>

                    <p class="text-gray-600 mb-6">
                        Nikmati perjalanan nyaman bersama {{ $flight->airline->name }} dengan armada terbaru dan pelayanan prima. 
                        Penerbangan langsung tanpa transit untuk menghemat waktu perjalanan Anda.
                    </p>

                    <!-- Flight Details Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Pesawat</p>
                            <p class="font-semibold text-gray-900">{{ $flight->airplane->model }}</p>
                            <p class="text-sm text-gray-500">{{ $flight->airplane->registration_number }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Durasi</p>
                            <p class="font-semibold text-gray-900">~{{ $flight->departure_datetime->diffInHours($flight->arrival_datetime) }} Jam</p>
                            <p class="text-sm text-gray-500">Estimasi terbang</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Bagasi</p>
                            <p class="font-semibold text-gray-900">20 Kg</p>
                            <p class="text-sm text-gray-500">Termasuk kabin 7kg</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase">Meal</p>
                            <p class="font-semibold text-gray-900">Included</p>
                            <p class="text-sm text-gray-500">Snack & Minuman</p>
                        </div>
                    </div>

                    <!-- Departure/Arrival Time -->
                    <div class="border-t pt-6">
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ $flight->departure_datetime->format('H:i') }}</p>
                                <p class="text-sm text-gray-500">{{ $flight->departure_datetime->format('d M Y') }}</p>
                                <p class="text-blue-600 font-semibold">{{ $flight->departureAirport->iata_code }}</p>
                            </div>
                            <div class="flex-1 mx-8 text-center">
                                <p class="text-sm text-gray-500">Durasi {{ $flight->departure_datetime->diffInHours($flight->arrival_datetime) }} Jam</p>
                                <div class="flex items-center justify-center my-2">
                                    <div class="h-px bg-gray-300 flex-1"></div>
                                    <i class="fas fa-plane text-blue-600 mx-2"></i>
                                    <div class="h-px bg-gray-300 flex-1"></div>
                                </div>
                                <p class="text-green-600 font-medium">Langsung</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ $flight->arrival_datetime->format('H:i') }}</p>
                                <p class="text-sm text-gray-500">{{ $flight->arrival_datetime->format('d M Y') }}</p>
                                <p class="text-blue-600 font-semibold">{{ $flight->arrivalAirport->iata_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Included Features -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Yang Termasuk dalam Tiket</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">Seat Selection</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">Check-in Baggage 20kg</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">In-flight Meal</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">Entertainment System</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">Asuransi Perjalanan</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-gray-700">E-Ticket & Boarding Pass</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Harga</h3>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga Tiket (1 Pax)</span>
                        <span class="font-semibold">Rp {{ number_format($flight->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak Bandara</span>
                        <span class="font-semibold">Rp 50.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Asuransi</span>
                        <span class="font-semibold text-green-600">Gratis</span>
                    </div>
                </div>

                <div class="border-t pt-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total Bayar</span>
                        <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($flight->price + 50000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <p class="text-sm text-blue-700">Harga sudah termasuk bagasi 20kg dan makan siang.</p>
                    </div>
                </div>

                <p class="text-xs text-gray-500 text-center mb-6">Dilindungi oleh garansi uang kembali Z-Airlines</p>

                @auth
                    <a href="{{ route('bookings.create', $flight->id) }}" class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center block">
                        Pesan Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center block">
                        Masuk untuk Memesan
                    </a>
                @endauth
            </div>

            <!-- Help Box -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h4 class="font-bold text-gray-900 mb-2">Butuh Bantuan?</h4>
                <p class="text-sm text-gray-600 mb-4">Tim kami siap membantu proses booking Anda 24 jam sehari.</p>
                <button class="w-full border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:border-blue-600 hover:text-blue-600 transition flex items-center justify-center">
                    <i class="fas fa-phone mr-2"></i>
                    Hubungi Call Center
                </button>
            </div>
        </div>
    </div>
</div>
@endsection