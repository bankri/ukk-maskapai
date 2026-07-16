<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airplanes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->string('model');
            $table->string('registration_number')->unique();
            $table->integer('capacity');
            $table->text('description')->nullable();
            $table->string('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airplanes');
    }
};