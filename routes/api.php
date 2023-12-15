<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'App\Http\Controllers\Api\Auth\AuthController@login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json(['data' => [$request->user()]]);
        });
        Route::post('/logout', 'App\Http\Controllers\Api\Auth\AuthController@logout');
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(['prefix' => 'routes'], function () {
        Route::get('/', 'App\Http\Controllers\Api\RouteController@index');
        Route::get('/{route}/seats', 'App\Http\Controllers\Api\RouteSeatController@index');
        Route::post('/{route}/seats/reserve', 'App\Http\Controllers\Api\RouteSeatController@store');
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::get('/', 'App\Http\Controllers\Api\CityController@index');
    });
});



