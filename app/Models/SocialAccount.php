<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    //
    protected $fillable = [
        'platform',
        'url',
    ];

public function accountable()
{
    return $this->morphTo();
}

}
