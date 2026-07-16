<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_id', 'airplane_id', 'departure_airport_id', 'arrival_airport_id',
        'departure_datetime', 'arrival_datetime', 'price', 'available_seats'
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function airplane()
    {
        return $this->belongsTo(Airplane::class);
    }

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}