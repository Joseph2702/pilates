<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instruktur extends Model
{
    protected $table = 'instruktur';

    protected $primaryKey = 'id_instruktur';

    public $timestamps = false;

    protected $fillable = ['id_user', 'spesialisasi'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKelas::class, 'id_instruktur', 'id_instruktur');
    }
}
