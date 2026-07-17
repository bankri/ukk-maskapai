<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Airplane;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $bookings = Booking::with([
            'user',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'flight.airline',
            'passengers',
            'payment',
            'approver',
        ])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->get();

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

            if ($fromStatus === 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'Booking yang sudah ditolak atau dibatalkan tidak dapat dibuka kembali.',
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

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'changed_by' => $request->user()->id,
                'from_status' => $fromStatus,
                'to_status' => $targetStatus,
                'note' => $targetStatus === 'confirmed'
                    ? 'Booking diterima admin dan siap dibayar melalui Midtrans.'
                    : 'Booking ditolak atau dibatalkan admin: '.$validated['rejected_reason'],
            ]);
        }, 3);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
