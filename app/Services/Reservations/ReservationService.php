<?php

namespace App\Services\Reservations;

use App\Models\UserReservation;
use App\Models\UserReservationsStop;
use App\Services\Reservations\Interfaces\ReservationInterface;
use App\Services\Seats\RouteSeatService;
use App\Services\Routes\RouteService;
use Illuminate\Support\Facades\DB;

class ReservationService implements ReservationInterface
{

    /**
     * @param RouteService $routeService
     */
    public function __construct(protected RouteService $routeService){}

    /** generate array of available seats
     * @param $routeId
     * @param $fromCityId
     * @param $toCityId
     * @return array
     */
    public function getAvailableSeatsOfRoute($routeId, $fromCityId, $toCityId): array
    {

        //get all trip seats
        $trip_seats = RouteSeatService::getRouteSeats($routeId);

        //get the needed stops between from and to cities
        $needed_stations = $this->routeService->getNeededStopsFromRoute($routeId, $fromCityId, $toCityId);

        // no stop selection no station will be needed
        if (empty($needed_stations))
            return [];

        $seats = [];
        foreach ($trip_seats as $seat){
            // check seat for the user route stations
            if (RouteSeatService::checkSeatReservations($seat->id, $needed_stations))
                $seats[] = $seat->id;
        }

        return  $seats;
    }

    /** create reservation
     * @param $routeId
     * @param $seatId
     * @param $fromCityId
     * @param $toCityId
     * @return bool
     */
    public function bookSeat($routeId, $seatId, $fromCityId, $toCityId, $user_id): bool
    {
        //check seat relation with the selected trip
        if (!RouteSeatService::checkSeatBelongsToRoute($seatId, $routeId))
            return false;

          // get the reservations stop
        $needed_stops = $this->routeService->getNeededStopsFromRoute($routeId, $fromCityId, $toCityId);

        // check if this reservation has no stations
        if (empty($needed_stops))
            return false;

        // check seat validation
        if (!RouteSeatService::checkSeatReservations($seatId, $needed_stops))
            return false;


        /*
         * use transactions when changes in many tables and once may be failed
         * so you need to rollback
         */
        DB::beginTransaction();

        // create new reservation
        $reservation = new UserReservation();
        $reservation->user_id = $user_id;
        $reservation->seat_id = $seatId;

        try {

            // check database saving
            if($reservation->save()){
                foreach ($needed_stops as $cityId){
                    UserReservationsStop::create([
                        'user_reservation_id' => $reservation->id,
                        'city_id' => $cityId,
                    ]);
                }

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;

        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
