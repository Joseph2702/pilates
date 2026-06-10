<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $primaryKey = 'id_promo';

    protected $fillable = [
        'kode_promo',
        'nama_promo',
        'persenan_diskon',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_promo',
    ];

    protected $casts = [
        'persenan_diskon' => 'decimal:2',
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];
}
