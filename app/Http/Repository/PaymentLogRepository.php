<?php

namespace App\Http\Repository;

use App\Domain\Entity\PaymentLog;

class PaymentLogRepository
{
    /** @param  array<string, mixed>  $rawResponse */
    public function record(string $orderId, array $rawResponse): PaymentLog
    {
        return PaymentLog::create([
            'order_id'     => $orderId,
            'raw_response' => $rawResponse,
        ]);
    }
}
