@extends('layouts.admin')

@section('title', 'Kelola Booking')
@section('page-title', 'Kelola Booking')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Kode</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">User</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Rute</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tanggal</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Pax</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Total</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 px-4 font-semibold text-blue-600">{{ $booking->booking_code }}</td>
                    <td class="py-4 px-4">{{ $booking->user->name }}</td>
                    <td class="py-4 px-4">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</td>
                    <td class="py-4 px-4">{{ $booking->created_at->format('d M Y') }}</td>
                    <td class="py-4 px-4">{{ $booking->total_passengers }}</td>
                    <td class="py-4 px-4 font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td class="py-4 px-4">
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-sm font-semibold border-0
                                @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td class="py-4 px-4">
                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection