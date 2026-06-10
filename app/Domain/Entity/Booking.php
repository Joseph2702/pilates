<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $table = 'booking';

    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'id_pelanggan',
        'id_jadwal_kelas',
        'status_booking',
        'tanggal_booking',
    ];

    protected $casts = [
        'tanggal_booking' => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function jadwalKelas(): BelongsTo
    {
        return $this->belongsTo(JadwalKelas::class, 'id_jadwal_kelas', 'id_jadwal_kelas');
    }

    public function absensi(): HasOne
    {
        return $this->hasOne(Absensi::class, 'id_booking', 'id_booking');
    }
}
