<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Airplane;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalFlights = Flight::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');

        $recentBookings = Booking::with(['user', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalFlights', 'totalBookings', 'totalRevenue', 'recentBookings'));
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
        $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'airplane_id' => 'required|exists:airplanes,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_datetime' => 'required|date|after:now',
            'arrival_datetime' => 'required|date|after:departure_datetime',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        Flight::create($request->all());

        return redirect()->route('admin.flights')->with('success', 'Penerbangan berhasil ditambahkan!');
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->latest()
            ->get();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Status booking berhasil diupdate!');
    }
}