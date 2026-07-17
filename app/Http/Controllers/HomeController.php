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
        // Temporary disable reviews - tabel belum ada
$reviews = collect([]);
$averageRating = 0;
$totalReviews = 0;
// Temporary - kolom completed_at belum ada
$completedTrips = 0;
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
