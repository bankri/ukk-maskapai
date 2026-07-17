<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'payment_status',
        'transaction_code',
        'order_id',
        'snap_token',
        'redirect_url',
        'transaction_id',
        'transaction_status',
        'fraud_status',
        'paid_at',
        'raw_notification',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_notification' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
