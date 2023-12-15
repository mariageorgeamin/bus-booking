<?php

namespace App\Services\Reservations\Interfaces;

interface ReservationInterface
{
    public function getAvailableSeatsOfRoute($routeId, $fromCityId, $toCityId);

    public function bookSeat($routeId, $seatId, $fromCityId, $toCityId, $user_id);
}
