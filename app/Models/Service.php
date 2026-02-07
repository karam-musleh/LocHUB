<?php

namespace App\Models;

use App\Policies\ServicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    protected $fillable = [
        'hup_id',
        'name',
        'description'
    ];
    //
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];
    protected $translation = [
        'name',
        'description'
    ];
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }
}
