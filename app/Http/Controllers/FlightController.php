<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function show($id)
    {
        $flight = Flight::with([
            'airline', 'airplane', 'departureAirport', 'arrivalAirport'
        ])->findOrFail($id);

        return view('flights.show', compact('flight'));
    }
}