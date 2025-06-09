<?php

namespace App\Enums;
enum RatingDirection: string
{
    case REFERRER_TO_REFERRED = 'referrer_to_referred';
    case REFERRED_TO_REFERRER = 'referred_to_referrer';
}
