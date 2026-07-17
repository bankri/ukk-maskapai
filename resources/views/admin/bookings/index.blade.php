@extends('layouts.admin')

@section('title', 'Kelola Booking')
@section('page-title', 'Kelola Booking')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Request Booking</h1>
        <p class="text-gray-500 mt-1">Periksa penumpang dan kursi sebelum menerima atau menolak.</p>
    </div>
    <form method="GET" class="flex gap-2">
        <select name="status" class="rounded-xl border-gray-300 px-4 py-2.5">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="confirmed" @selected(request('status') === 'confirmed')>Diterima</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Ditolak</option>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2.5 rounded-xl">Filter</button>
    </form>
</div>

<div class="space-y-5">
    @forelse($bookings as $booking)
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-bold text-blue-700">{{ $booking->booking_code }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($booking->status) }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $booking->payment?->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">Bayar: {{ ucfirst($booking->payment?->payment_status ?? '-') }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">{{ $booking->user->name }} · {{ $booking->user->email }}</p>
                    </div>
                    <p class="text-sm text-gray-500">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
                    <div class="lg:col-span-2"><p class="text-xs text-gray-500">Rute</p><p class="font-semibold">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p></div>
                    <div><p class="text-xs text-gray-500">Tanggal</p><p class="font-semibold">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                    <div><p class="text-xs text-gray-500">Pax</p><p class="font-semibold">{{ $booking->total_passengers }}</p></div>
                    <div><p class="text-xs text-gray-500">Total</p><p class="font-bold text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 mb-6">
                    @foreach($booking->passengers as $passenger)
                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <div class="flex justify-between gap-3"><strong>{{ $passenger->full_name }}</strong><span class="font-bold text-blue-600">{{ $passenger->seat_number }}</span></div>
                            <p class="text-sm text-gray-600 mt-2">{{ $passenger->gender === 'male' ? 'Laki-laki' : 'Perempuan' }} · {{ $passenger->birth_date->format('d M Y') }}</p>
                            <p class="text-sm text-gray-600 break-all">{{ strtoupper($passenger->identity_type ?? 'identitas') }}: {{ $passenger->identity_number ?? $passenger->passport_number }}</p>
                        </div>
                    @endforeach
                </div>

                @if($booking->status === 'pending')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 border-t pt-5">
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700"><i class="fas fa-check mr-2"></i>Terima Booking</button>
                        </form>
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <textarea name="rejected_reason" required rows="2" maxlength="1000" placeholder="Alasan penolakan" class="w-full rounded-xl border-gray-300 px-4 py-3"></textarea>
                            <button class="w-full bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700"><i class="fas fa-xmark mr-2"></i>Tolak Booking</button>
                        </form>
                    </div>
                @elseif($booking->status === 'confirmed' && $booking->payment?->payment_status !== 'paid')
                    <div class="border-t pt-5">
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" class="max-w-xl space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <textarea name="rejected_reason" required rows="2" maxlength="1000" placeholder="Alasan pembatalan sebelum pembayaran" class="w-full rounded-xl border-gray-300 px-4 py-3"></textarea>
                            <button class="bg-red-600 text-white px-5 py-3 rounded-xl font-semibold">Batalkan Booking</button>
                        </form>
                    </div>
                @elseif($booking->payment?->payment_status === 'paid')
                    <div class="border-t pt-5 text-sm text-green-700 font-semibold"><i class="fas fa-circle-check mr-2"></i>Pembayaran telah terverifikasi.</div>
                @endif
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl p-10 text-center text-gray-500">Tidak ada booking pada filter ini.</div>
    @endforelse
</div>
@endsection
