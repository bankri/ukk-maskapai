<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'flight_id', 'booking_code', 'total_passengers', 'total_price', 'status'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}