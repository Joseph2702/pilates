<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalKelas extends Model
{
    protected $table = 'jadwal_kelas';

    protected $primaryKey = 'id_jadwal_kelas';

    protected $fillable = [
        'id_kelas',
        'id_instruktur',
        'tanggal_kelas',
        'jam_mulai',
        'jam_selesai',
        'kuota_maksimal',
        'kuota_terisi',
    ];

    protected $casts = [
        'tanggal_kelas' => 'datetime',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_jadwal_kelas', 'id_jadwal_kelas');
    }
}
