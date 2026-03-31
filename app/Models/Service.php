<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    //
    use HasTranslations;
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];
    protected $translatable = ['name', 'description'];
    public function hubs()
    {
        return $this->belongsToMany(Hub::class)->withTimestamps();
    }
}
