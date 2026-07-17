<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    public function createSnapTransaction(Booking $booking): array
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $booking->loadMissing(['user', 'flight.departureAirport', 'flight.arrivalAirport', 'payment']);
        $payment = $booking->payment;

        if (! $payment) {
            throw new RuntimeException('Data pembayaran booking tidak ditemukan.');
        }

        $endpoint = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $routeName = $booking->flight->departureAirport->city.' - '.$booking->flight->arrivalAirport->city;

        $payload = [
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'item_details' => [[
                'id' => $booking->booking_code,
                'price' => (int) round((float) $booking->flight->price),
                'quantity' => $booking->total_passengers,
                'name' => mb_substr('Tiket '.$routeName, 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'credit_card' => [
                'secure' => true,
            ],
            'callbacks' => [
                'finish' => route('payments.finish', $booking),
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(20)
                ->retry(2, 500)
                ->post($endpoint, $payload)
                ->throw();
        } catch (RequestException $exception) {
            $message = $exception->response?->json('error_messages.0')
                ?? $exception->response?->body()
                ?? $exception->getMessage();

            throw new RuntimeException('Midtrans menolak transaksi: '.$message, previous: $exception);
        }

        $token = $response->json('token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Midtrans tidak mengembalikan Snap Token.');
        }

        return [
            'token' => $token,
            'redirect_url' => $response->json('redirect_url'),
        ];
    }

    public function snapScriptUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function signatureIsValid(array $payload): bool
    {
        $required = ['order_id', 'status_code', 'gross_amount', 'signature_key'];

        foreach ($required as $field) {
            if (! isset($payload[$field])) {
                return false;
            }
        }

        $expected = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key')
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }
}
