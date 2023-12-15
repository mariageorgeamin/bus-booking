<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReservation extends Model
{
    use HasFactory;

    protected $table = 'user_reservations';

    protected $fillable = ['user_id', 'seat_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function seat()
    {
        return $this->belongsTo(RouteSeat::class, 'seat_id', 'id');
    }
}
