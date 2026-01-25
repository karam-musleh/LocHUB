<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
