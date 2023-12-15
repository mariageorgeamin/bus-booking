<?php

namespace Tests\Feature\Reservations;

use App\Models\Bus;
use App\Models\City;
use App\Models\Route;
use App\Models\RouteStation;
use App\Models\User;
use App\Services\Reservations\ReservationService;
use App\Services\Routes\RouteService;
use Tests\TestCase;

class ReservationsTest extends TestCase
{
    protected $reservationService;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->reservationService = new ReservationService(new RouteService());


        parent::__construct($name, $data, $dataName);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_available_seats_of_route()
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

        $this->assertEquals(
            count($this->reservationService->getAvailableSeatsOfRoute($route->id, $from->id, $to->id)),
            $bus->seats_capacity);

        $user = User::factory()->create();
        // do reserve
        $this->reservationService->bookSeat($route->id, $seats[2]->id, $from->id, $to->id, $user->id);

        $this->assertEquals(
            count($this->reservationService->getAvailableSeatsOfRoute($route->id, $from->id, $to->id)),
            $bus->seats_capacity - 1);

        // do reserve
        $this->reservationService->bookSeat($route->id, $seats[2]->id, $from->id, $in->id, $user->id);


        $this->assertEquals(
            count($this->reservationService->getAvailableSeatsOfRoute($route->id, $in->id, $to->id)),
            $bus->seats_capacity - 1);

    }


    public function test_book_set()
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

        // get seats
        //

        $this->assertEquals(
            count($this->reservationService->getAvailableSeatsOfRoute($route->id, $from->id, $to->id)), $bus->seats_capacity);

        $user = User::factory()->create();
        // first reservation of seat
        $this->assertTrue($this->reservationService->bookSeat($route->id, $seats[2]->id, $from->id, $to->id, $user->id));

        // check second attempt of the same reservation
        $this->assertFalse($this->reservationService->bookSeat($route->id, $seats[2]->id, $from->id, $to->id, $user->id));


        // reserve the half route
        $this->assertTrue($this->reservationService->bookSeat($route->id, $seats[1]->id, $from->id, $in->id, $user->id));
        $this->assertTrue($this->reservationService->bookSeat($route->id, $seats[1]->id, $in->id, $to->id, $user->id));


        // reserve last two station then first two station

        // reserve the half route
        $this->assertTrue($this->reservationService->bookSeat($route->id, $seats[0]->id, $in->id, $to->id, $user->id));
        $this->assertTrue($this->reservationService->bookSeat($route->id, $seats[0]->id, $from->id, $in->id, $user->id));
    }
}
