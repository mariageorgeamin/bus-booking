<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteSeat extends Model
{
    use HasFactory;

    public function reservations(){
        return $this->hasMany(UserReservation::class, 'seat_id', 'id');
    }
}
