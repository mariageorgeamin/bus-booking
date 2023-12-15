<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\City;
use App\Models\Route;
use App\Models\RouteStation;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // seed init cities
        \App\Models\City::factory()->create(['name' => 'Cairo']);
        \App\Models\City::factory()->create(['name' => 'Giza']);
        \App\Models\City::factory()->create(['name' => 'AlFayyum']);
        \App\Models\City::factory()->create(['name' => 'AlMinya']);
        \App\Models\City::factory()->create(['name' => 'Asyut']);

        // create bus
        $bus = Bus::factory()->create([
            'name' => 'Cairo Bus',
            'seats_capacity' => 12,
        ]);

        // attach a route to bus
        $route = Route::factory()->create([
            'name' => 'Cairo Asyut Route',
            'bus_id' => $bus->id
        ]);

        // get some or all cities to create the route
        $cities = City::all();
        foreach ($cities as $key => $city){
            RouteStation::factory()->create([
                'route_id' => $route->id,
                'city_id' => $city->id,
                'station_order' => $key
            ]);
        }

    }
}
