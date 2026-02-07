<?php

namespace App\Enum;

enum OfferStatus: string
{
    //
    case SOON = 'soon'; // قريباً
    case ACTIVE = 'active'; // نشط
    case EXPIRED = 'expired'; // منتهي
    
}
