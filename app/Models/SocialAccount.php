<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    //
    protected $fillable = [
        'hub_id',
        'platform',
        'url',
    ];
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
