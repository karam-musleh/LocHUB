<?php

namespace App\Models;

use App\Enum\ImageType;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable = [
        'path',
        'type',
    ];

    protected $casts = [
        'type' => ImageType::class,
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
