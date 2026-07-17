<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flight_id',
        'booking_code',
        'total_passengers',
        'total_price',
        'status',
        'approved_by',
        'approved_at',
        'completed_at',
        'rejected_reason',
        'seats_released_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'seats_released_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function histories()
    {
        return $this->hasMany(BookingStatusHistory::class)->latest();
    }

    public function review()
    {
        return $this->hasOne(BookingReview::class);
    }

    public function isPayable(): bool
    {
        return $this->status === 'confirmed'
            && ! $this->completed_at
            && $this->payment
            && $this->payment->payment_status === 'pending';
    }

    public function canBeReviewed(): bool
    {
        return (bool) $this->completed_at
            && $this->payment?->payment_status === 'paid'
            && ! $this->review;
    }
}
