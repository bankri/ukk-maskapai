<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Airport;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('available_seats', '>', 0)
            ->orderBy('departure_datetime', 'asc')
            ->limit(6)
            ->get();

        $airports = Airport::all();

        return view('home.index', compact('flights', 'airports'));
    }

    public function search(Request $request)
    {
        $query = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('available_seats', '>', 0);

        if ($request->filled('from')) {
            $query->where('departure_airport_id', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('arrival_airport_id', $request->to);
        }

        if ($request->filled('date')) {
            $query->whereDate('departure_datetime', $request->date);
        }

        $flights = $query->orderBy('departure_datetime', 'asc')->get();
        $airports = Airport::all();

        return view('flights.index', compact('flights', 'airports'));
    }
}