<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    public $timestamps = false;

    protected $fillable = ['order_id', 'raw_response'];

    protected $casts = [
        'raw_response' => 'array',
    ];
}
