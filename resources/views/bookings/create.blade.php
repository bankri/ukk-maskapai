@extends('layouts.app')

@section('title', 'Buat Booking')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Form Pemesanan</h1>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <!-- Flight Summary -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-4">Detail Penerbangan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Rute</p>
                    <p class="font-semibold">{{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="font-semibold">{{ $flight->departure_datetime->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Waktu</p>
                    <p class="font-semibold">{{ $flight->departure_datetime->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Harga/Pax</p>
                    <p class="font-semibold text-blue-600">Rp {{ number_format($flight->price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
            @csrf
            <input type="hidden" name="flight_id" value="{{ $flight->id }}">

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penumpang</label>
                <select id="passengerCount" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="generatePassengerForms()">
                    <option value="1">1 Penumpang</option>
                    <option value="2">2 Penumpang</option>
                    <option value="3">3 Penumpang</option>
                    <option value="4">4 Penumpang</option>
                    <option value="5">5 Penumpang</option>
                </select>
            </div>

            <div id="passengerForms" class="space-y-6">
                <!-- Passenger forms will be generated here -->
            </div>

            <div class="border-t pt-6 mt-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xl font-bold text-gray-900">Total Harga</span>
                    <span id="totalPrice" class="text-3xl font-bold text-blue-600">Rp {{ number_format($flight->price, 0, ',', '.') }}</span>
                </div>

                <div class="flex space-x-4">
                    <a href="{{ route('flights.show', $flight->id) }}" class="flex-1 border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-semibold hover:border-blue-600 hover:text-blue-600 text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-lg font-semibold hover:bg-blue-700">
                        Lanjutkan ke Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function generatePassengerForms() {
    const count = document.getElementById('passengerCount').value;
    const container = document.getElementById('passengerForms');
    const price = {{ $flight->price }};
    
    container.innerHTML = '';
    for (let i = 1; i <= count; i++) {
        container.innerHTML += `
            <div class="border border-gray-200 rounded-lg p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Penumpang ${i}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="passengers[${i-1}][full_name]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                        <select name="passengers[${i-1}][gender]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="passengers[${i-1}][birth_date]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Paspor</label>
                        <input type="text" name="passengers[${i-1}][passport_number]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        `;
    }
    
    document.getElementById('totalPrice').textContent = 'Rp ' + (price * count).toLocaleString('id-ID');
}

// Initialize with 1 passenger
generatePassengerForms();
</script>
@endpush
@endsection