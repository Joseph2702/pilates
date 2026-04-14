<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembelianPackage extends Model
{
    protected $table = 'pembelian_package';
    protected $primaryKey = 'id_pembelian_package';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'id_package',
        'id_promo',
        'harga_awal',
        'diskon',
        'harga_akhir',
        'status_pembelian',
        'kredit_earned',
        'sisa_kredit',
        'tanggal_pembelian',
        'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'harga_awal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'harga_akhir' => 'decimal:2',
        'tanggal_pembelian' => 'datetime',
        'tanggal_kadaluarsa' => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package', 'id_package');
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id_promo');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_pembelian_package', 'id_pembelian_package');
    }
}
