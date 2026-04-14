<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'id_pembelian_package',
        'jumlah_bayar',
        'midtrans_order_id',
        'snap_token',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'payment_response',
        'status_internal',
        'expired_at',
        'created_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'payment_response' => 'array',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function pembelianPackage(): BelongsTo
    {
        return $this->belongsTo(PembelianPackage::class, 'id_pembelian_package', 'id_pembelian_package');
    }
}
