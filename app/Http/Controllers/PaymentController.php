<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentController extends Controller
{
    public function show(Booking $booking, MidtransService $midtrans)
    {
        $this->authorizeBooking($booking);

        $booking->load([
            'user',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'flight.airline',
            'passengers',
            'payment',
        ]);

        if ($booking->status !== 'confirmed') {
            return redirect()->route('bookings.index')
                ->with('error', 'Pembayaran hanya tersedia setelah booking diterima admin.');
        }

        if ($booking->payment?->payment_status === 'paid') {
            return redirect()->route('bookings.index')
                ->with('success', 'Booking ini sudah berstatus Terbayar.');
        }

        if (! $booking->payment) {
            return redirect()->route('bookings.index')
                ->with('error', 'Data pembayaran booking tidak ditemukan.');
        }

        if (! $booking->payment->snap_token) {
            try {
                $transaction = $midtrans->createSnapTransaction($booking);
                $booking->payment->update([
                    'snap_token' => $transaction['token'],
                    'redirect_url' => $transaction['redirect_url'],
                ]);
            } catch (RuntimeException $exception) {
                report($exception);

                return redirect()->route('bookings.index')->with('error', $exception->getMessage());
            }
        }

        return view('payments.show', [
            'booking' => $booking->fresh(['payment', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers']),
            'snapScriptUrl' => $midtrans->snapScriptUrl(),
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }

    public function finish(Booking $booking, MidtransService $midtrans)
    {
        $this->authorizeBooking($booking);
        $booking->load('payment');

        if (! $booking->payment) {
            return redirect()->route('bookings.index')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        try {
            $payload = $midtrans->getTransactionStatus($booking->payment->order_id);
            $status = $this->persistPaymentStatus($booking->payment, $payload, 'Get Status API Midtrans');
        } catch (RuntimeException $exception) {
            report($exception);

            return redirect()->route('bookings.index')
                ->with('error', 'Pembayaran telah ditutup, tetapi status belum dapat disinkronkan. Gunakan tombol Perbarui Status.');
        }

        return redirect()->route('bookings.index')->with(
            $status === 'paid' ? 'success' : 'error',
            $status === 'paid'
                ? 'Pembayaran berhasil diverifikasi. Status booking sekarang Terbayar.'
                : 'Status pembayaran masih menunggu atau belum berhasil. Silakan perbarui kembali beberapa saat lagi.'
        );
    }

    public function sync(Booking $booking, MidtransService $midtrans)
    {
        $this->authorizeBooking($booking);
        $booking->load('payment');

        if (! $booking->payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        try {
            $payload = $midtrans->getTransactionStatus($booking->payment->order_id);
            $status = $this->persistPaymentStatus($booking->payment, $payload, 'Sinkronisasi manual Midtrans');
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            $status === 'paid' ? 'success' : 'error',
            $status === 'paid'
                ? 'Status pembayaran berhasil diperbarui menjadi Terbayar.'
                : 'Status pembayaran saat ini: '.ucfirst($status).'.'
        );
    }

    public function notification(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->signatureIsValid($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('order_id', $payload['order_id'])->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        try {
            $this->persistPaymentStatus($payment, $payload, 'Webhook Midtrans');
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['message' => 'Notification processed']);
    }

    private function persistPaymentStatus(Payment $payment, array $payload, string $source): string
    {
        if (isset($payload['gross_amount'])) {
            $incomingAmount = (int) round(((float) $payload['gross_amount']) * 100);
            $expectedAmount = (int) round(((float) $payment->amount) * 100);

            if ($incomingAmount !== $expectedAmount) {
                throw new RuntimeException('Nominal transaksi Midtrans tidak sesuai dengan booking.');
            }
        }

        return DB::transaction(function () use ($payment, $payload, $source) {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $previousStatus = $payment->payment_status;
            $transactionStatus = (string) ($payload['transaction_status'] ?? 'unknown');
            $fraudStatus = (string) ($payload['fraud_status'] ?? '');

            $newStatus = match (true) {
                $transactionStatus === 'settlement' => 'paid',
                $transactionStatus === 'capture' && in_array($fraudStatus, ['', 'accept'], true) => 'paid',
                in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true) => 'failed',
                default => 'pending',
            };

            if ($previousStatus === 'paid' && $newStatus !== 'paid') {
                $newStatus = 'paid';
            }

            $payment->update([
                'payment_method' => $payload['payment_type'] ?? $payment->payment_method,
                'payment_status' => $newStatus,
                'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus ?: null,
                'paid_at' => $newStatus === 'paid' ? ($payment->paid_at ?? now()) : $payment->paid_at,
                'raw_notification' => $payload,
            ]);

            if ($previousStatus !== $newStatus) {
                BookingStatusHistory::create([
                    'booking_id' => $payment->booking_id,
                    'changed_by' => null,
                    'from_status' => 'payment_'.$previousStatus,
                    'to_status' => 'payment_'.$newStatus,
                    'note' => $source.' memperbarui status pembayaran menjadi '.($newStatus === 'paid' ? 'Terbayar' : ucfirst($newStatus)).'.',
                    'metadata' => [
                        'transaction_status' => $transactionStatus,
                        'transaction_id' => $payload['transaction_id'] ?? null,
                    ],
                ]);
            }

            return $newStatus;
        }, 3);
    }

    private function authorizeBooking(Booking $booking): void
    {
        abort_unless($booking->user_id === request()->user()->id, 403);
    }
}
