<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    //
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // موقع الهب
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // خدمات الهب
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // عروض الهب
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    // حجوزات الهب
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // تقييمات الهب
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // صور الهب (Morph)
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
