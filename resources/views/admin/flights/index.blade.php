@extends('layouts.admin')

@section('title', 'Kelola Penerbangan')
@section('page-title', 'Kelola Penerbangan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div></div>
    <a href="{{ route('admin.flights.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Penerbangan
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">ID</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Maskapai</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Rute</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tanggal</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Harga</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Seat</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 px-4">{{ $flight->id }}</td>
                    <td class="py-4 px-4">{{ $flight->airline->name }}</td>
                    <td class="py-4 px-4">{{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}</td>
                    <td class="py-4 px-4">{{ $flight->departure_datetime->format('d M Y H:i') }}</td>
                    <td class="py-4 px-4 font-semibold">Rp {{ number_format($flight->price, 0, ',', '.') }}</td>
                    <td class="py-4 px-4">{{ $flight->available_seats }}</td>
                    <td class="py-4 px-4">
                        <button class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></button>
                        <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection