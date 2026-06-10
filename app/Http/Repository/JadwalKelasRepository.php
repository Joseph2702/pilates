<?php

namespace App\Http\Repository;

use App\Domain\Entity\JadwalKelas;
use Illuminate\Database\Eloquent\Collection;

class JadwalKelasRepository
{
    /** @return Collection<int, JadwalKelas> */
    public function listUpcoming(): Collection
    {
        return JadwalKelas::where('tanggal_kelas', '>=', now())
            ->orderBy('tanggal_kelas')
            ->get();
    }

    public function findById(int $id): ?JadwalKelas
    {
        return JadwalKelas::find($id);
    }

    /**
     * SELECT ... FOR UPDATE — call inside a DB transaction so concurrent
     * BookingService::book() can't oversell capacity.
     */
    public function findForBooking(int $id): ?JadwalKelas
    {
        return JadwalKelas::whereKey($id)->lockForUpdate()->first();
    }

    public function incrementKuotaTerisi(JadwalKelas $jadwal): void
    {
        $jadwal->increment('kuota_terisi');
    }

    public function decrementKuotaTerisi(JadwalKelas $jadwal): void
    {
        $jadwal->decrement('kuota_terisi');
    }
}
