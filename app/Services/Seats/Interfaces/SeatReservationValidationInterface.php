<?php

namespace App\Services\Seats\Interfaces;

interface SeatReservationValidationInterface
{
    /** check route with $routeId is the parent of the seat with $seatId
     * @param $seatId
     * @param $routeId
     * @return bool
     */
    public static  function checkSeatBelongsToRoute($seatId, $routeId): bool;


    /** check if a seat not available with some cities
     * @param $seatId
     * @param $needed_stations
     * @return bool
     */
    public static function checkSeatReservations($seatId, $needed_stations): bool;
}
