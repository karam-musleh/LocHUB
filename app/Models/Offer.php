<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Offer extends Model
{
    use HasTranslations ;
    //

protected $fillable = [

'hub_id',
'title',
'type',
'price',
'duration',
'description',
'status',
];
protected $translation = [
    'title',
    'description'
];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
