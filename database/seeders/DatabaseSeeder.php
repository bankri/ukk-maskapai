<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Airplane;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Z-Airlines',
            'email' => 'admin@zairlines.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Regular User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // Create Airlines
        $garuda = Airline::create(['name' => 'Garuda Indonesia', 'code' => 'GA', 'description' => 'Flag carrier Indonesia']);
        $lion = Airline::create(['name' => 'Lion Air', 'code' => 'JT', 'description' => 'Low cost carrier']);
        $batik = Airline::create(['name' => 'Batik Air', 'code' => 'ID', 'description' => 'Full service carrier']);

        // Create Airports
        $cgk = Airport::create(['name' => 'Soekarno-Hatta', 'city' => 'Jakarta', 'country' => 'Indonesia', 'iata_code' => 'CGK']);
        $dps = Airport::create(['name' => 'Ngurah Rai', 'city' => 'Denpasar', 'country' => 'Indonesia', 'iata_code' => 'DPS']);
        $sub = Airport::create(['name' => 'Juanda', 'city' => 'Surabaya', 'country' => 'Indonesia', 'iata_code' => 'SUB']);
        $kul = Airport::create(['name' => 'Kuala Lumpur Intl', 'city' => 'Kuala Lumpur', 'country' => 'Malaysia', 'iata_code' => 'KUL']);
        $sin = Airport::create(['name' => 'Changi', 'city' => 'Singapore', 'country' => 'Singapore', 'iata_code' => 'SIN']);
        $dxb = Airport::create(['name' => 'Dubai Intl', 'city' => 'Dubai', 'country' => 'UAE', 'iata_code' => 'DXB']);

        // Create Airplanes
        $boeing1 = Airplane::create(['airline_id' => $garuda->id, 'model' => 'Boeing 737-800', 'registration_number' => 'PK-GMA', 'capacity' => 160]);
        $boeing2 = Airplane::create(['airline_id' => $garuda->id, 'model' => 'Airbus A330-300', 'registration_number' => 'PK-GPI', 'capacity' => 280]);
        $boeing3 = Airplane::create(['airline_id' => $lion->id, 'model' => 'Boeing 737-900ER', 'registration_number' => 'PK-LKR', 'capacity' => 180]);

        // Create Flights
        Flight::create([
            'airline_id' => $garuda->id,
            'airplane_id' => $boeing1->id,
            'departure_airport_id' => $cgk->id,
            'arrival_airport_id' => $dps->id,
            'departure_datetime' => now()->addDays(2)->setHour(8)->setMinute(0),
            'arrival_datetime' => now()->addDays(2)->setHour(11)->setMinute(30),
            'price' => 1500000,
            'available_seats' => 120,
        ]);

        Flight::create([
            'airline_id' => $garuda->id,
            'airplane_id' => $boeing2->id,
            'departure_airport_id' => $cgk->id,
            'arrival_airport_id' => $kul->id,
            'departure_datetime' => now()->addDays(3)->setHour(10)->setMinute(0),
            'arrival_datetime' => now()->addDays(3)->setHour(13)->setMinute(0),
            'price' => 2800000,
            'available_seats' => 200,
        ]);

        Flight::create([
            'airline_id' => $batik->id,
            'airplane_id' => $boeing1->id,
            'departure_airport_id' => $sub->id,
            'arrival_airport_id' => $dps->id,
            'departure_datetime' => now()->addDays(1)->setHour(14)->setMinute(0),
            'arrival_datetime' => now()->addDays(1)->setHour(15)->setMinute(30),
            'price' => 1200000,
            'available_seats' => 100,
        ]);

        Flight::create([
            'airline_id' => $garuda->id,
            'airplane_id' => $boeing2->id,
            'departure_airport_id' => $cgk->id,
            'arrival_airport_id' => $dxb->id,
            'departure_datetime' => now()->addDays(5)->setHour(22)->setMinute(0),
            'arrival_datetime' => now()->addDays(6)->setHour(6)->setMinute(0),
            'price' => 9982800,
            'available_seats' => 250,
        ]);

        Flight::create([
            'airline_id' => $lion->id,
            'airplane_id' => $boeing3->id,
            'departure_airport_id' => $cgk->id,
            'arrival_airport_id' => $sin->id,
            'departure_datetime' => now()->addDays(4)->setHour(9)->setMinute(0),
            'arrival_datetime' => now()->addDays(4)->setHour(12)->setMinute(0),
            'price' => 3500000,
            'available_seats' => 150,
        ]);
    }
}