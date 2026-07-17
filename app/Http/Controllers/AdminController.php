<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Airplane;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalFlights = Flight::count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalRevenue = Booking::whereHas('payment', fn ($query) => $query->where('payment_status', 'paid'))
            ->sum('total_price');

        $recentBookings = Booking::with([
            'user',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'payment',
        ])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalFlights',
            'totalBookings',
            'pendingBookings',
            'totalRevenue',
            'recentBookings'
        ));
    }

    public function flights()
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'airplane'])
            ->latest()
            ->get();

        return view('admin.flights.index', compact('flights'));
    }

    public function createFlight()
    {
        $airlines = Airline::all();
        $airports = Airport::all();
        $airplanes = Airplane::all();

        return view('admin.flights.create', compact('airlines', 'airports', 'airplanes'));
    }

    public function storeFlight(Request $request)
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'airplane_id' => 'required|exists:airplanes,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_datetime' => 'required|date|after:now',
            'arrival_datetime' => 'required|date|after:departure_datetime',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        Flight::create($validated);

        return redirect()->route('admin.flights')->with('success', 'Penerbangan berhasil ditambahkan.');
    }

    public function bookings(Request $request)
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
                'user',
                'flight.departureAirport',
                'flight.arrivalAirport',
                'flight.airline',
                'passengers',
                'payment',
                // 'approver', // Disabled - relationship belum ada
                // 'review',   // Disabled - tabel booking_reviews belum ada
            ])
            ->when(filled($validated['q'] ?? null), function ($query) use ($validated) {
                $like = '%'.Str::lower(trim($validated['q'])).'%';

                $query->where(function ($search) use ($like) {
                    $search->whereRaw('LOWER(booking_code) LIKE ?', [$like])
                        ->orWhereHas('user', fn ($user) => $user
                            ->whereRaw('LOWER(name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$like]))
                        ->orWhereHas('passengers', fn ($passenger) => $passenger
                            ->whereRaw('LOWER(full_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(identity_number) LIKE ?', [$like]))
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
            'rejected_reason' => ['nullable', 'required_if:status,cancelled', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $validated, $request) {
            $booking = Booking::query()
                ->with('payment')
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $flight = Flight::query()->lockForUpdate()->findOrFail($booking->flight_id);
            $fromStatus = $booking->status;
            $targetStatus = $validated['status'];

            if ($fromStatus === $targetStatus) {
                return;
            }

            if ($fromStatus === 'cancelled' || $booking->completed_at) {
                throw ValidationException::withMessages([
                    'status' => 'Booking yang sudah dibatalkan atau selesai tidak dapat diubah kembali.',
                ]);
            }

            if ($targetStatus === 'cancelled' && $booking->payment?->payment_status === 'paid') {
                throw ValidationException::withMessages([
                    'status' => 'Booking yang sudah dibayar tidak dapat dibatalkan dari halaman ini.',
                ]);
            }

            if ($targetStatus === 'confirmed') {
                $booking->fill([
                    'status' => 'confirmed',
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                    'rejected_reason' => null,
                ]);
            } else {
                $booking->fill([
                    'status' => 'cancelled',
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                    'rejected_reason' => $validated['rejected_reason'],
                ]);

                if (! $booking->seats_released_at) {
                    $flight->increment('available_seats', $booking->total_passengers);
                    $booking->seats_released_at = now();
                }
            }

            $booking->save();

            // BookingStatusHistory::create([ ... ]); // Disabled sementara agar tidak error jika tabel belum ada
        }, 3);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function completeBooking(Request $request, Booking $booking)
    {
        DB::transaction(function () use ($request, $booking) {
            $booking = Booking::query()
                ->with('payment')
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if ($booking->status !== 'confirmed' || $booking->payment?->payment_status !== 'paid') {
                throw ValidationException::withMessages([
                    'status' => 'Booking hanya dapat diselesaikan setelah diterima dan berstatus Terbayar.',
                ]);
            }

            if ($booking->completed_at) {
                return;
            }

            $booking->update(['completed_at' => now()]);

            // BookingStatusHistory::create([ ... ]); // Disabled sementara agar tidak error jika tabel belum ada
        }, 3);

        return back()->with('success', 'Booking ditandai selesai.');
    }
}