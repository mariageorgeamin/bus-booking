<?php

namespace App\Services\Routes;

use App\Models\RouteStation;
use App\Services\Routes\Interfaces\RouteServiceInterface;

class RouteService implements RouteServiceInterface
{
    /** get route stations
     * @param $routeId
     * @return array
     */
    public function getRouteStationsOrders($routeId): array
    {
        return RouteStation::query()
            ->where('route_id', '=', $routeId)
            ->orderBy('station_order')
            ->pluck('city_id')
            ->toArray();
    }

    /** check the arrival and departure station for a route
     * @param $routeId
     * @param $fromCityId
     * @param $toCityId
     * @return bool
     */
    public function validateNeededRoute($routeId, $fromCityId, $toCityId): bool
    {

        // the city of arrival can not be the same as  city of  departure station
        if ($fromCityId == $toCityId)
            return false;

        // get the stations route
        $trip_stations = $this->getRouteStationsOrders($routeId);

        // check that from to is included the trip
        if (!in_array($fromCityId, $trip_stations) || !in_array($toCityId, $trip_stations))
            return false;

        /**
         * check the rout of the stations departure
         */
        $from = array_search($fromCityId, $trip_stations);
        $to = array_search($toCityId, $trip_stations);

        // the city of arrival can not be before the city of  departure station or the same station
        if ($from >= $to)
            return  false;

        return true;
    }

    /** this method help to generate the need stations for a selected route
     * to use for create reservation stops and checking
     * @param $routeId
     * @param $fromCityId
     * @param $toCityId
     * @return bool
     */
    public function getNeededStopsFromRoute($routeId, $fromCityId, $toCityId): array
    {
        // validate the correct trips and needed from to
        if (!$this->validateNeededRoute($routeId, $fromCityId, $toCityId)) return [];

        // get the stations route
        $trip_stations = $this->getRouteStationsOrders($routeId);

        // identify the positions of the stations
        $from = array_search($fromCityId, $trip_stations);
        $to = array_search($toCityId, $trip_stations);

        // return the stations of a selected trip from to
        return array_slice($trip_stations, $from, $to - $from);
    }
}
