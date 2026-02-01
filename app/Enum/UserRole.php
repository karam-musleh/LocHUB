<?php

namespace App\Enum;

enum UserRole:string
{
    //
    case ADMIN = 'admin';
    case USER = 'user';
    case HUB_OWNER = 'hub_owner';
}
