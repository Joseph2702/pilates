<?php

namespace App\Domain\Enums;

enum BookingStatus: string
{
    case BOOKED   = 'booked';
    case ATTENDED = 'attended';
    case NO_SHOW  = 'no_show';
    case CANCELED = 'canceled';
}
