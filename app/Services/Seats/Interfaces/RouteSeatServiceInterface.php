<?php

namespace App\Services\Seats\Interfaces;

interface RouteSeatServiceInterface extends SeatReservationValidationInterface
{
    /** get seat of route
     * @param $routeId
     * @return mixed
     */
    public static  function getRouteSeats($routeId);
}
