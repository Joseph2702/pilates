<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $timestamps = false;

    protected $fillable = ['id_user', 'tanggal_daftar'];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pembelianPackage(): HasMany
    {
        return $this->hasMany(PembelianPackage::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function mutasiKredit(): HasMany
    {
        return $this->hasMany(MutasiKredit::class, 'id_pelanggan', 'id_pelanggan');
    }
}
