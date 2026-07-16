<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $bookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        $totalBookings = Booking::where('user_id', Auth::id())->count();
        $confirmedBookings = Booking::where('user_id', Auth::id())->where('status', 'confirmed')->count();

        return view('user.dashboard', compact('bookings', 'totalBookings', 'confirmedBookings'));
    }
}