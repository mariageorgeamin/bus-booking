<?php

namespace App\Services\Routes\Interfaces;

interface RouteReservationFromToValidationInterface
{
    /** check if seat can be booked for specific city
     * @param $routeId
     * @param $fromCityId
     * @param $toCityId
     * @return bool
     */
    public function validateNeededRoute($routeId, $fromCityId, $toCityId): bool;
}
