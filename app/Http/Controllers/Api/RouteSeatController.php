<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRouteSeatRequest;
use App\Http\Requests\ReserveRouteSeatRequest;
use App\Models\Route;
use App\Services\Reservations\ReservationService;
use Illuminate\Http\Request;

class RouteSeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Route $route,GetRouteSeatRequest $request)
    {

        $seats_ids = app(ReservationService::class)->getAvailableSeatsOfRoute($route->id, $request->from_city_id, $request->to_city_id);
        return response()->json(['data' => ['seats_ids' => $seats_ids]]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Route $route, ReserveRouteSeatRequest $request)
    {
        $routeId = $route->id;
        $userId = auth()->user()->id;
        $check = app(ReservationService::class)->bookSeat($routeId, $request->seat_id, $request->from_city_id, $request->to_city_id,$userId);
        if($check)
            return response()->json(['data' => [ 'message' => __('created') ]]);

        return response()->json(['data' => [ 'message' => __('Seat reserved') ]], 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
