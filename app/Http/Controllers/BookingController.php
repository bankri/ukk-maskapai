<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function create($flightId)
    {
        $flight = Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'airplane'])
            ->findOrFail($flightId);

        return view('bookings.create', compact('flight'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.full_name' => 'required|string',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.birth_date' => 'required|date',
            'passengers.*.passport_number' => 'required|string',
        ]);

        $flight = Flight::findOrFail($request->flight_id);
        $totalPassengers = count($request->passengers);
        $totalPrice = $flight->price * $totalPassengers;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'flight_id' => $flight->id,
            'booking_code' => 'Z' . strtoupper(Str::random(8)),
            'total_passengers' => $totalPassengers,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        foreach ($request->passengers as $passenger) {
            Passenger::create([
                'booking_id' => $booking->id,
                'full_name' => $passenger['full_name'],
                'gender' => $passenger['gender'],
                'birth_date' => $passenger['birth_date'],
                'passport_number' => $passenger['passport_number'],
            ]);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'bank_transfer',
            'amount' => $totalPrice,
            'payment_status' => 'pending',
            'transaction_code' => 'TRX' . strtoupper(Str::random(10)),
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dibuat!');
    }
}