<?php

namespace App\Domain\Enums;

enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case PAID     = 'paid';
    case FAILED   = 'failed';
    case EXPIRED  = 'expired';
    case REFUNDED = 'refunded';
    case CANCELED = 'canceled';
}
