<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\Flight;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('available_seats', '>', 0)
            ->where('departure_datetime', '>', now())
            ->orderBy('departure_datetime')
            ->limit(6)
            ->get();

        $airports = Airport::orderBy('city')->get();
        $reviews = BookingReview::with([
            'user',
            'booking.flight.departureAirport',
            'booking.flight.arrivalAirport',
        ])
            ->whereNotNull('comment')
            ->latest()
            ->limit(6)
            ->get();

        $averageRating = round((float) BookingReview::avg('rating'), 1);
        $totalReviews = BookingReview::count();
        $completedTrips = Booking::whereNotNull('completed_at')->count();

        return view('home.index', compact(
            'flights',
            'airports',
            'reviews',
            'averageRating',
            'totalReviews',
            'completedTrips'
        ));
    }

    public function search(Request $request)
    {
        $query = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('available_seats', '>', 0)
            ->where('departure_datetime', '>', now());

        if ($request->filled('from')) {
            $query->where('departure_airport_id', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('arrival_airport_id', $request->to);
        }

        if ($request->filled('date')) {
            $query->whereDate('departure_datetime', $request->date);
        }

        $flights = $query->orderBy('departure_datetime')->get();
        $airports = Airport::orderBy('city')->get();

        return view('flights.index', compact('flights', 'airports'));
    }
}
