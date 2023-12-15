<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReservationsStop extends Model
{
    use HasFactory;

    protected $table = 'user_reservation_stops';
    protected $fillable = ['user_reservation_id', 'city_id'];

    public function reservation()
    {
        return $this->belongsTo(UserReservation::class, 'user_reservation_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
