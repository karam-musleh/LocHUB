<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Enum\LocationType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    //
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    protected $fillable = [
        'name',
        'parent_id',
        'type',
    ];
    protected $casts = [
        'type' => LocationType::class,
        'name' => 'array',
        'slug' => 'string',
    ];
    public $translatable = ['name'];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id')
            ->with('children');
    }

    public function hubs()
    {
        return $this->hasMany(Hub::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
