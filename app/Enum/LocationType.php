<?php

namespace App\Enum;

enum LocationType: string
{
    case GOVERNORATE = 'governorate';
    case CITY        = 'city';
    case AREA        = 'area';
}
