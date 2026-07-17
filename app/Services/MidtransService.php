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
        $serverKey = $this->serverKey();
        $booking->loadMissing(['user', 'flight.departureAirport', 'flight.arrivalAirport', 'payment']);
        $payment = $booking->payment;

        if (! $payment) {
            throw new RuntimeException('Data pembayaran booking tidak ditemukan.');
        }

        $routeName = $booking->flight->departureAirport->city.' - '.$booking->flight->arrivalAirport->city;
        $payload = [
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'item_details' => [[
                'id' => $booking->booking_code,
                'price' => (int) round((float) $payment->amount),
                'quantity' => 1,
                'name' => mb_substr('Tiket '.$routeName.' ('.$booking->total_passengers.' pax)', 0, 50),
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
                ->post($this->snapBaseUrl().'/snap/v1/transactions', $payload)
                ->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('Midtrans menolak transaksi: '.$this->errorMessage($exception), previous: $exception);
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

    public function getTransactionStatus(string $orderId): array
    {
        try {
            return Http::acceptJson()
                ->withBasicAuth($this->serverKey(), '')
                ->timeout(15)
                ->retry(2, 400)
                ->get($this->apiBaseUrl().'/v2/'.rawurlencode($orderId).'/status')
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            throw new RuntimeException('Status Midtrans belum dapat diperbarui: '.$this->errorMessage($exception), previous: $exception);
        }
    }

    public function snapScriptUrl(): string
    {
        return $this->snapBaseUrl().'/snap/snap.js';
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
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$this->serverKey()
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    private function serverKey(): string
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        return $serverKey;
    }

    private function snapBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    private function apiBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    private function errorMessage(RequestException $exception): string
    {
        return $exception->response?->json('error_messages.0')
            ?? $exception->response?->json('status_message')
            ?? $exception->response?->body()
            ?? $exception->getMessage();
    }
}
