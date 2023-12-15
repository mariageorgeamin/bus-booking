<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RouteStation extends Model
{
    Use HasFactory;
    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
