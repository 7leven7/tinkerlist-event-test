<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ['name' => 'Brussels', 'latitude' => 50.8503, 'longitude' => 4.3517],
            ['name' => 'Antwerp', 'latitude' => 51.2199, 'longitude' => 4.4035],
            ['name' => 'Ghent', 'latitude' => 51.0543, 'longitude' => 3.7174],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }
    }
}

