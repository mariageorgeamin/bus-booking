<?php

namespace App\Services\Seats;

use App\Models\RouteSeat;
use App\Models\UserReservationsStop;
use App\Services\Seats\Interfaces\SeatReservationValidationInterface;
use App\Services\Seats\Interfaces\RouteSeatServiceInterface;

class RouteSeatService implements RouteSeatServiceInterface
{
    /** get the seats of route
     * @param $routeId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static  function getRouteSeats($routeId){
        return RouteSeat::query()
            ->where('route_id', '=', $routeId)
            ->with('reservations')
            ->get();
    }

    /**
     * @param $seatId
     * @param $needed_stations
     * @return bool
     */
    public static function checkSeatReservations($seatId, $needed_stations): bool
    {
        return !UserReservationsStop::query()
            ->leftJoin('user_reservations',
                'user_reservations.id', '=', 'user_reservation_stops.user_reservation_id')
            ->where('user_reservations.seat_id','=', $seatId)
            ->whereIn('user_reservation_stops.city_id', $needed_stations)->count();
    }

    /** check a seat of a trip
     * @param $routeId
     * @param $seatId
     * @return bool
     */
    public static function checkSeatBelongsToRoute($seatId, $routeId): bool
    {
        // seat validation
        if(! $seat = RouteSeat::find($seatId))
            return false;

        // seat route id
        if ($seat->route_id == $routeId)
            return true;

        return false;
    }
}
