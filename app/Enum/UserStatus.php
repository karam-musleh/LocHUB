<?php

namespace App\Enum;


enum UserStatus: string
{
    //
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
}
