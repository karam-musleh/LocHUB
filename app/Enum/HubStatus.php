<?php

namespace App\Enum;


enum HubStatus: string
{
    //
    case PENDING = 'pending'; // قيد الانتظار
    case REJECTED = 'rejected'; //مرفوض
    case APPROVED = 'approved'; //معتمد
}
