<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    //

    public function initiativeSocialAccounts()
    {
        return $this->morphMany(SocialAccount::class, 'accountable');
    }

}
    