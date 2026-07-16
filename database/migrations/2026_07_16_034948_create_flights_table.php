<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('airplane_id')->constrained()->onDelete('cascade');
            $table->foreignId('departure_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('arrival_airport_id')->constrained('airports')->onDelete('cascade');
            $table->dateTime('departure_datetime');
            $table->dateTime('arrival_datetime');
            $table->decimal('price', 12, 2);
            $table->integer('available_seats')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};