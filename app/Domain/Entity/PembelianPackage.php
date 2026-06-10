<?php

namespace App\Domain\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_pembelian_package
 * @property int $id_pelanggan
 * @property int $id_package
 * @property int|null $id_promo
 * @property string $harga_awal
 * @property string $diskon
 * @property string $harga_akhir
 * @property string $status_pembelian
 * @property int $kredit_earned
 * @property int $sisa_kredit
 * @property Carbon $tanggal_pembelian
 * @property Carbon|null $tanggal_kadaluarsa
 */
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

    /**
     * Remaining credit for this package.
     * sisa_kredit is maintained by CreditService::syncSisaKreditFIFO on every booking/refund.
     */
    public function getRemainingCredit(): int
    {
        return max(0, min((int) $this->sisa_kredit, (int) $this->kredit_earned));
    }
}
