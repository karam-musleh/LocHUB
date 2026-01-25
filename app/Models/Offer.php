<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
