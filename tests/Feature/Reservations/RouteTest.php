<?php

namespace Tests\Feature\Reservations;

use App\Models\Bus;
use App\Models\City;
use App\Models\Route;
use App\Models\RouteSeat;
use App\Models\RouteStation;
use App\Services\Routes\RouteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;


    public function test_get_route_stations_orders(){
        // seed init cities
        $from = \App\Models\City::factory()->create(['name' => 'Cairo']);
        $in = \App\Models\City::factory()->create(['name' => 'AlMinya']);
        $to = \App\Models\City::factory()->create(['name' => 'Asyut']);

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
        $cities = [$from, $in, $to];
        foreach ($cities as $key => $city){
            RouteStation::factory()->create([
                'route_id' => $route->id,
                'city_id' => $city->id,
                'station_order' => $key
            ]);
        }

        // generate the route seats
        RouteSeat::factory($bus->seats_capacity)->create([
            'route_id' => $route->id
        ]);

        $routeService = new RouteService();
        $this->assertEquals($routeService->getRouteStationsOrders($route->id), [$from->id, $in->id, $to->id]);
    }

    public function test_get_needed_stops_from_route(){
        // seed init cities
        $from = \App\Models\City::factory()->create(['name' => 'Cairo']);
        $to = \App\Models\City::factory()->create(['name' => 'Giza']);
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

        // generate the route seats
        RouteSeat::factory($bus->seats_capacity)->create([
            'route_id' => $route->id
        ]);

        $routeService = new RouteService();
        $this->assertEquals($routeService->getNeededStopsFromRoute($route->id,$from->id,$to->id), [$from->id]);
        $this->assertEquals($routeService->getNeededStopsFromRoute($route->id,$from->id,$to->id +1), [$from->id, $to->id]);

    }

    public function test_validate_route(){
        // seed init cities
        $from = \App\Models\City::factory()->create(['name' => 'Cairo']);
        $to = \App\Models\City::factory()->create(['name' => 'Giza']);
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

        // generate the route seats
        RouteSeat::factory($bus->seats_capacity)->create([
            'route_id' => $route->id
        ]);

        $routeService = new RouteService();

        // check same value from
        $this->assertFalse($routeService->validateNeededRoute($route->id,$from->id, $from->id));

        // check same value to
        $this->assertFalse($routeService->validateNeededRoute($route->id,$to->id, $to->id));

        // cities not included
        $this->assertFalse($routeService->validateNeededRoute($route->id, $from->id + 100, $to->id + 100));

        // reversed route
        $this->assertFalse($routeService->validateNeededRoute($route->id,$to->id, $from->id));

        // normal case
        $this->assertTrue($routeService->validateNeededRoute($route->id,$from->id,$to->id +1));
    }

}
