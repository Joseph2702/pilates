<?php

namespace App\Http\Repository;

use App\Domain\Entity\Absensi;
use Illuminate\Database\Eloquent\Collection;

class AbsensiRepository
{
    public function findByBookingId(int $idBooking): ?Absensi
    {
        return Absensi::where('id_booking', $idBooking)->first();
    }

    public function listByJadwal(int $idJadwalKelas): Collection
    {
        return Absensi::whereHas('booking', function ($q) use ($idJadwalKelas) {
            $q->where('id_jadwal_kelas', $idJadwalKelas);
        })->with('booking.pelanggan.user')->get();
    }

    public function createOrUpdate(int $idBooking, string $statusKehadiran): Absensi
    {
        return Absensi::updateOrCreate(
            ['id_booking' => $idBooking],
            ['status_kehadiran' => $statusKehadiran],
        );
    }
}
