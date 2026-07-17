@extends('layouts.app')

@section('title', 'Pembayaran')

@push('head')
<script src="{{ $snapScriptUrl }}" data-client-key="{{ $clientKey }}"></script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-900 text-white p-6 sm:p-8">
            <p class="text-blue-200 text-sm">Pembayaran Booking</p>
            <h1 class="text-2xl sm:text-3xl font-bold mt-1">{{ $booking->booking_code }}</h1>
            <p class="text-blue-100 mt-3">{{ $booking->flight->departureAirport->city }} → {{ $booking->flight->arrivalAirport->city }}</p>
        </div>
        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-2 gap-4 mb-7">
                <div><p class="text-sm text-gray-500">Penumpang</p><p class="font-semibold">{{ $booking->total_passengers }} orang</p></div>
                <div><p class="text-sm text-gray-500">Keberangkatan</p><p class="font-semibold">{{ $booking->flight->departure_datetime->format('d M Y, H:i') }}</p></div>
                <div class="col-span-2 rounded-xl bg-blue-50 p-5"><p class="text-sm text-gray-500">Total Pembayaran</p><p class="text-3xl font-bold text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 mb-6">
                Jangan menutup halaman sampai Snap Midtrans terbuka. Status final pembayaran disimpan melalui webhook Midtrans.
            </div>

            <button id="payButton" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200">
                <i class="fas fa-shield-halved mr-2"></i>Bayar Sekarang
            </button>
            <a href="{{ route('bookings.index') }}" class="block text-center mt-4 text-gray-600 hover:text-blue-600">Kembali ke Booking Saya</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const payButton = document.getElementById('payButton');
    const finishUrl = @json(route('payments.finish', $booking));
    const snapToken = @json($booking->payment->snap_token);

    payButton.addEventListener('click', () => {
        if (!window.snap) {
            alert('Snap Midtrans belum siap. Periksa Client Key atau koneksi internet.');
            return;
        }

        payButton.disabled = true;
        payButton.classList.add('opacity-60', 'cursor-not-allowed');

        window.snap.pay(snapToken, {
            onSuccess: () => window.location.href = finishUrl,
            onPending: () => window.location.href = finishUrl,
            onError: () => {
                payButton.disabled = false;
                payButton.classList.remove('opacity-60', 'cursor-not-allowed');
                alert('Pembayaran gagal diproses. Silakan coba lagi.');
            },
            onClose: () => {
                payButton.disabled = false;
                payButton.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        });
    });
</script>
@endpush
