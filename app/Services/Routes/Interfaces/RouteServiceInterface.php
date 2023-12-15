<?php

namespace App\Services\Routes\Interfaces;

interface RouteServiceInterface extends RouteReservationFromToValidationInterface
{
    /** stations of a route
     * @param $routeId
     * @return array
     */
    public function getRouteStationsOrders($routeId): array;

    public function getNeededStopsFromRoute($routeId, $fromCityId, $toCityId): array;
}
