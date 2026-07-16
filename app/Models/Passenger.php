<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'full_name', 'gender', 'birth_date', 'passport_number', 'seat_number'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}