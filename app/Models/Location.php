<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //



    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function hubs()
    {
        return $this->hasMany(Hub::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
