<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = ['nama_kelas', 'deskripsi', 'kapasitas'];

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKelas::class, 'id_kelas', 'id_kelas');
    }
}
