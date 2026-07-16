<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airplane extends Model
{
    use HasFactory;

    protected $fillable = ['airline_id', 'model', 'registration_number', 'capacity', 'description', 'photos'];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}