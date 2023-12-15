<?php

namespace Tests\Feature\Reservations;

use App\Models\Bus;
use App\Models\City;
use App\Models\Route;
use App\Models\RouteSeat;
use App\Models\RouteStation;
use App\Models\User;
use App\Services\Seats\RouteSeatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSeatsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_route_seats()
    {
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
        $cities = City::all();
        foreach ($cities as $key => $city){
            RouteStation::factory()->create([
                'route_id' => $route->id,
                'city_id' => $city->id,
                'station_order' => $key
            ]);
        }

        // generate the route seats
        $seats = $route->seats;

        // check columns
        $needed = ['id', 'route_id', 'city_id', 'station_order'];
        $this->assertEquals(RouteSeatService::getRouteSeats($route->id)->pluck($needed), $seats->pluck($needed));
    }

    public function test_check_seat_reservation()
    {
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
        $cities = City::all();
        foreach ($cities as $key => $city) {
            RouteStation::factory()->create([
                'route_id' => $route->id,
                'city_id' => $city->id,
                'station_order' => $key
            ]);
        }

        // generate the route seats
        $seats = RouteSeat::factory($bus->seats_capacity)->create([
            'route_id' => $route->id
        ]);

        $user = User::factory()->create();
        $check = (new \App\Services\Reservations\ReservationService(new \App\Services\Routes\RouteService()))
            ->bookSeat($route->id, $seats[2]->id, $from->id, $to->id, $user->id);

        $this->assertTrue($check);

        // reserved seat
        $this->assertFalse(RouteSeatService::checkSeatReservations($seats[2]->id, [$from->id, $in->id]));

        // unreserved seat
        $this->assertTrue(RouteSeatService::checkSeatReservations($seats[1]->id, [$from->id, $in->id]));
    }

    public function test_check_seat_belong_toRoute()
    {
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
        $cities = City::all();
        foreach ($cities as $key => $city) {
            RouteStation::factory()->create([
                'route_id' => $route->id,
                'city_id' => $city->id,
                'station_order' => $key
            ]);
        }

        // generate the route seats
        $seats = RouteSeat::factory($bus->seats_capacity)->create([
            'route_id' => $route->id
        ]);

        $this->assertTrue(RouteSeatService::checkSeatBelongsToRoute($seats[1]->id, $route->id));
        $this->assertFalse(RouteSeatService::checkSeatBelongsToRoute($seats[1]->id, $route->id - 1));


    }
}
