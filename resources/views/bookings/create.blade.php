@extends('layouts.app')

@section('title', 'Buat Booking')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="mb-8">
        <p class="text-blue-600 font-semibold mb-2">Request Booking</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Isi Data Penumpang</h1>
        <p class="text-gray-500 mt-2">Maksimal lima penumpang. Booking akan diperiksa admin sebelum pembayaran dibuka.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
        <div class="bg-blue-50 rounded-xl p-5 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <h3 class="font-bold text-gray-900">Detail Penerbangan</h3>
                <span class="text-sm font-semibold text-blue-700 bg-white rounded-full px-3 py-1">{{ $flight->available_seats }} kursi tersedia</span>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div><p class="text-xs sm:text-sm text-gray-500">Rute</p><p class="font-semibold">{{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}</p></div>
                <div><p class="text-xs sm:text-sm text-gray-500">Tanggal</p><p class="font-semibold">{{ $flight->departure_datetime->format('d M Y') }}</p></div>
                <div><p class="text-xs sm:text-sm text-gray-500">Waktu</p><p class="font-semibold">{{ $flight->departure_datetime->format('H:i') }}</p></div>
                <div><p class="text-xs sm:text-sm text-gray-500">Harga/Pax</p><p class="font-semibold text-blue-600">Rp {{ number_format($flight->price, 0, ',', '.') }}</p></div>
            </div>
        </div>

        <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
            @csrf
            <input type="hidden" name="flight_id" value="{{ $flight->id }}">

            <div class="mb-6 max-w-xs">
                <label for="passengerCount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penumpang</label>
                <select id="passengerCount" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    @for($count = 1; $count <= min(5, $flight->available_seats); $count++)
                        <option value="{{ $count }}">{{ $count }} Penumpang</option>
                    @endfor
                </select>
            </div>

            <div id="passengerForms" class="space-y-6"></div>

            <div class="border-t pt-6 mt-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
                    <span class="text-lg font-bold text-gray-900">Total Harga</span>
                    <span id="totalPrice" class="text-2xl sm:text-3xl font-bold text-blue-600"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('flights.show', $flight->id) }}" class="border-2 border-gray-300 text-gray-700 py-3.5 rounded-xl font-semibold hover:border-blue-600 hover:text-blue-600 text-center">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white py-3.5 rounded-xl font-semibold hover:bg-blue-700">Ajukan Booking</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const pricePerPassenger = @json((float) $flight->price);
    const oldPassengers = @json(old('passengers', []));
    const passengerCount = document.getElementById('passengerCount');
    const passengerForms = document.getElementById('passengerForms');
    const maxPassengers = Math.max(1, Math.min(5, Number(@json($flight->available_seats))));

    if (oldPassengers.length > 0) {
        passengerCount.value = String(Math.min(oldPassengers.length, maxPassengers));
    }

    const escapeHtml = (value = '') => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    function passengerCard(index, data = {}) {
        const gender = data.gender ?? 'male';
        const identityType = data.identity_type ?? 'ktp';

        return `
            <section class="border border-gray-200 rounded-2xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h4 class="font-bold text-gray-900">Penumpang ${index + 1}</h4>
                    <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-3 py-1">Data wajib lengkap</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="passengers[${index}][full_name]" value="${escapeHtml(data.full_name)}" required maxlength="120"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" autocomplete="name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                        <select name="passengers[${index}][gender]" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="male" ${gender === 'male' ? 'selected' : ''}>Laki-laki</option>
                            <option value="female" ${gender === 'female' ? 'selected' : ''}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="passengers[${index}][birth_date]" value="${escapeHtml(data.birth_date)}" required max="{{ now()->subDay()->format('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Identitas</label>
                        <select name="passengers[${index}][identity_type]" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="ktp" ${identityType === 'ktp' ? 'selected' : ''}>KTP / NIK</option>
                            <option value="passport" ${identityType === 'passport' ? 'selected' : ''}>Paspor</option>
                            <option value="other" ${identityType === 'other' ? 'selected' : ''}>Identitas Lain</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Identitas</label>
                        <input type="text" name="passengers[${index}][identity_number]" value="${escapeHtml(data.identity_number)}" required maxlength="64"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kursi</label>
                        <input type="text" name="passengers[${index}][seat_number]" value="${escapeHtml(data.seat_number)}" required maxlength="3" pattern="[1-9][0-9]?[A-Fa-f]"
                            placeholder="Contoh: 12A" class="w-full px-4 py-3 uppercase border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Kursi harus unik untuk penerbangan ini.</p>
                    </div>
                </div>
            </section>`;
    }

    function renderPassengerForms() {
        const count = Number(passengerCount.value);
        passengerForms.innerHTML = Array.from({ length: count }, (_, index) => passengerCard(index, oldPassengers[index] ?? {})).join('');
        document.getElementById('totalPrice').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(pricePerPassenger * count);
    }

    passengerCount.addEventListener('change', renderPassengerForms);
    renderPassengerForms();
</script>
@endpush
