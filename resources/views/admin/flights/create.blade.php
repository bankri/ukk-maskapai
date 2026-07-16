@extends('layouts.admin')

@section('title', 'Tambah Penerbangan')
@section('page-title', 'Tambah Penerbangan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-plus text-blue-600 mr-3"></i>
            Tambah Paket Penerbangan
        </h2>

        <form method="POST" action="{{ route('admin.flights.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bandara Asal *</label>
                    <select name="departure_airport_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bandara Tujuan *</label>
                    <select name="arrival_airport_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->iata_code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Penerbangan *</label>
                    <input type="text" name="flight_number" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: ZGA001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Berangkat *</label>
                    <input type="datetime-local" name="departure_datetime" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesawat *</label>
                    <select name="airplane_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Pesawat</option>
                        @foreach($airplanes as $airplane)
                            <option value="{{ $airplane->id }}">{{ $airplane->model }} - {{ $airplane->registration_number }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Tiket (Rp) *</label>
                    <input type="number" name="price" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="5000000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Kursi Tersedia *</label>
                    <input type="number" name="available_seats" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="150">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar Destinasi</label>
                <input type="url" name="image_url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://images.unsplash.com/photo-...">
                <p class="text-sm text-gray-500 mt-1">💡 Copy link gambar dari Pinterest/Unsplash. Format: JPG, PNG, WEBP.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Penerbangan</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi rute, fasilitas, dll..."></textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.flights') }}" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 flex items-center">
                    <i class="fas fa-check mr-2"></i>
                    Simpan Penerbangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection