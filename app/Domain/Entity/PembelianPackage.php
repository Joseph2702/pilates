<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Entity\MutasiKredit;

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
     * Calculate remaining credit for this purchase based on ledger (mutasi_kredit).
     * - Debit (booking) reduces remaining credit
     * - Credit (booking_refund) restores remaining credit
     * Mutations since this purchase date are assumed to come from this purchase.
     */
    public function getRemainingCredit(): int
    {
        // Sum all debits (booking) since this purchase
        $totalDebit = MutasiKredit::where('id_pelanggan', $this->id_pelanggan)
            ->where('jenis_mutasi', 'debit')
            ->where('sumber_mutasi', 'booking')
            ->where('tanggal_mutasi', '>=', $this->tanggal_pembelian)
            ->sum('jumlah_kredit');

        // Sum all credits (booking refunds) since this purchase
        $totalRefund = MutasiKredit::where('id_pelanggan', $this->id_pelanggan)
            ->where('jenis_mutasi', 'credit')
            ->where('sumber_mutasi', 'booking_refund')
            ->where('tanggal_mutasi', '>=', $this->tanggal_pembelian)
            ->sum('jumlah_kredit');

        return max(0, $this->kredit_earned - $totalDebit + $totalRefund);
    }
}
