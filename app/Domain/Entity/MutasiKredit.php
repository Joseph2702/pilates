<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiKredit extends Model
{
    protected $table = 'mutasi_kredit';
    protected $primaryKey = 'id_mutasi';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'jenis_mutasi',
        'jumlah_kredit',
        'sumber_mutasi',
        'id_referensi',
        'keterangan',
        'tanggal_mutasi',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
