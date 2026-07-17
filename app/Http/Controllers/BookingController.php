<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
            'status' => ['nullable', 'in:pending,confirmed,cancelled'],
            'payment_status' => ['nullable', 'in:pending,paid,failed'],
        ]);

        $bookings = Booking::query()
            ->with([
                'flight.departureAirport',
                'flight.arrivalAirport',
                'flight.airline',
                'passengers',
                'payment',
                // 'histories.actor',  // Disabled - model belum ada
                // 'review',           // Disabled - tabel booking_reviews belum ada
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
                $query->where('status', $validated['status']);
            })
            ->when(filled($validated['payment_status'] ?? null), fn ($query) => $query
                ->whereHas('payment', fn ($payment) => $payment->where('payment_status', $validated['payment_status'])))
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
            'passengers.*.identity_number' => ['required', 'string', 'max:64'],
            'passengers.*.seat_number' => ['required', 'string', 'max:10'],
        ], [
            'passengers.max' => 'Maksimal 5 penumpang dalam satu booking.',
        ]);

        $booking = DB::transaction(function () use ($validated) {
            $flight = Flight::query()->lockForUpdate()->findOrFail($validated['flight_id']);
            $totalPassengers = count($validated['passengers']);

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

            foreach ($validated['passengers'] as $passenger) {
                Passenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => trim($passenger['full_name']),
                    'gender' => $passenger['gender'],
                    'birth_date' => $passenger['birth_date'],
                    'passport_number' => trim($passenger['identity_number']),
                    'seat_number' => Str::upper(trim($passenger['seat_number'])),
                ]);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'bank_transfer',
                'amount' => $totalPrice,
                'payment_status' => 'pending',
                'transaction_code' => 'TRX'.Str::upper(Str::random(10)),
            ]);

            $flight->decrement('available_seats', $totalPassengers);

            return $booking;
        }, 3);

        return redirect()
            ->route('bookings.index')
            ->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
    }
}