<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations ;
    protected $fillable = [
        'hup_id',
        'name',
        'description'
    ];
    //
    protected $translation =[
        'name',
        'description'
    ];
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
