<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,cancelled,completed'],
            'payment_status' => ['nullable', 'in:pending,paid,failed'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $bookings = Booking::query()
            ->with([
                'flight.departureAirport',
                'flight.arrivalAirport',
                'flight.airline',
                'passengers',
                'payment',
                'histories.actor',
                'review',
            ])
            ->where('user_id', Auth::id())
            ->when(filled($validated['q'] ?? null), function ($query) use ($validated) {
                $like = '%'.Str::lower(trim($validated['q'])).'%';

                $query->where(function ($search) use ($like) {
                    $search->whereRaw('LOWER(booking_code) LIKE ?', [$like])
                        ->orWhereHas('passengers', fn ($passenger) => $passenger
                            ->whereRaw('LOWER(full_name) LIKE ?', [$like]))
                        ->orWhereHas('flight.departureAirport', fn ($airport) => $airport
                            ->whereRaw('LOWER(city) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(iata_code) LIKE ?', [$like]))
                        ->orWhereHas('flight.arrivalAirport', fn ($airport) => $airport
                            ->whereRaw('LOWER(city) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(iata_code) LIKE ?', [$like]));
                });
            })
            ->when(filled($validated['status'] ?? null), function ($query) use ($validated) {
                if ($validated['status'] === 'completed') {
                    $query->whereNotNull('completed_at');
                } else {
                    $query->where('status', $validated['status']);
                }
            })
            ->when(filled($validated['payment_status'] ?? null), fn ($query) => $query
                ->whereHas('payment', fn ($payment) => $payment->where('payment_status', $validated['payment_status'])))
            ->when(filled($validated['date_from'] ?? null), fn ($query) => $query
                ->whereHas('flight', fn ($flight) => $flight->whereDate('departure_datetime', '>=', $validated['date_from'])))
            ->when(filled($validated['date_to'] ?? null), fn ($query) => $query
                ->whereHas('flight', fn ($flight) => $flight->whereDate('departure_datetime', '<=', $validated['date_to'])))
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create($flightId)
    {
        $flight = Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'airplane'])
            ->findOrFail($flightId);

        abort_if($flight->departure_datetime->isPast(), 422, 'Penerbangan sudah tidak dapat dipesan.');
        abort_if($flight->available_seats < 1, 422, 'Kursi penerbangan sudah habis.');

        return view('bookings.create', compact('flight'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flight_id' => ['required', 'integer', 'exists:flights,id'],
            'passengers' => ['required', 'array', 'min:1', 'max:5'],
            'passengers.*.full_name' => ['required', 'string', 'max:120'],
            'passengers.*.gender' => ['required', 'in:male,female'],
            'passengers.*.birth_date' => ['required', 'date', 'before:today'],
            'passengers.*.identity_type' => ['required', 'in:ktp,passport,other'],
            'passengers.*.identity_number' => ['required', 'string', 'max:64', 'distinct'],
            'passengers.*.seat_number' => ['required', 'regex:/^[1-9][0-9]?[A-F]$/i', 'distinct'],
        ], [
            'passengers.max' => 'Maksimal 5 penumpang dalam satu booking.',
            'passengers.*.seat_number.regex' => 'Format kursi harus seperti 1A, 12C, atau 20F.',
        ]);

        $passengers = collect($validated['passengers'])
            ->map(function (array $passenger) {
                $passenger['seat_number'] = Str::upper(trim($passenger['seat_number']));
                $passenger['identity_number'] = trim($passenger['identity_number']);

                return $passenger;
            });

        $booking = DB::transaction(function () use ($validated, $passengers) {
            $flight = Flight::query()->lockForUpdate()->findOrFail($validated['flight_id']);
            $totalPassengers = $passengers->count();

            if ($flight->departure_datetime->isPast()) {
                throw ValidationException::withMessages([
                    'flight_id' => 'Penerbangan sudah tidak dapat dipesan.',
                ]);
            }

            if ($flight->available_seats < $totalPassengers) {
                throw ValidationException::withMessages([
                    'passengers' => 'Kursi tersedia tidak mencukupi jumlah penumpang.',
                ]);
            }

            $requestedSeats = $passengers->pluck('seat_number')->all();
            $occupiedSeats = Passenger::query()
                ->whereIn('seat_number', $requestedSeats)
                ->whereHas('booking', function ($query) use ($flight) {
                    $query->where('flight_id', $flight->id)
                        ->where('status', '!=', 'cancelled');
                })
                ->pluck('seat_number')
                ->all();

            if ($occupiedSeats !== []) {
                throw ValidationException::withMessages([
                    'passengers' => 'Kursi '.implode(', ', $occupiedSeats).' sudah digunakan pada penerbangan ini.',
                ]);
            }

            do {
                $bookingCode = 'ZA'.Str::upper(Str::random(8));
            } while (Booking::where('booking_code', $bookingCode)->exists());

            $totalPrice = (float) $flight->price * $totalPassengers;

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'flight_id' => $flight->id,
                'booking_code' => $bookingCode,
                'total_passengers' => $totalPassengers,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($passengers as $passenger) {
                Passenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => trim($passenger['full_name']),
                    'gender' => $passenger['gender'],
                    'birth_date' => $passenger['birth_date'],
                    'passport_number' => $passenger['identity_number'],
                    'identity_type' => $passenger['identity_type'],
                    'identity_number' => $passenger['identity_number'],
                    'seat_number' => $passenger['seat_number'],
                ]);
            }

            $orderId = $bookingCode.'-'.now()->format('YmdHis');

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'midtrans',
                'amount' => $totalPrice,
                'payment_status' => 'pending',
                'transaction_code' => $orderId,
                'order_id' => $orderId,
            ]);

            $flight->decrement('available_seats', $totalPassengers);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'changed_by' => Auth::id(),
                'from_status' => null,
                'to_status' => 'pending',
                'note' => 'Booking diajukan oleh user dan menunggu persetujuan admin.',
                'metadata' => ['reserved_seats' => $requestedSeats],
            ]);

            return $booking;
        }, 3);

        return redirect()
            ->route('bookings.index')
            ->with('success', "Booking {$booking->booking_code} berhasil diajukan. Tunggu persetujuan admin sebelum membayar.");
    }
}
