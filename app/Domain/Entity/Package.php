<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $table = 'package';
    protected $primaryKey = 'id_package';

    protected $fillable = [
        'nama_package',
        'jumlah_kredit',
        'harga',
        'masa_berlaku',
        'status_package',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'jumlah_kredit' => 'integer',
        'masa_berlaku' => 'integer',
    ];

    public function pembelian(): HasMany
    {
        return $this->hasMany(PembelianPackage::class, 'id_package', 'id_package');
    }
}
