<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Absensi;
use App\Http\Repository\AbsensiRepository;
use App\Http\Repository\BookingRepository;
use App\Http\Repository\JadwalKelasRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AbsensiService
{
    public function __construct(
        protected AbsensiRepository $repository,
        protected BookingRepository $bookingRepo,
        protected JadwalKelasRepository $jadwalRepo,
    ) {}

    public function listByJadwal(int $idJadwalKelas): Collection
    {
        return $this->repository->listByJadwal($idJadwalKelas);
    }

    public function markAttendance(int $idBooking, string $statusKehadiran): Absensi
    {
        $booking = $this->bookingRepo->findById($idBooking)
            ?? throw new BusinessException('Booking tidak ditemukan', 404);

        if ($booking->status_booking === 'canceled') {
            throw new BusinessException('Tidak bisa absensi untuk booking yang sudah dibatalkan', 422);
        }

        // Check if class is currently ongoing (between jam_mulai and jam_selesai)
        $jadwal = $this->jadwalRepo->findById($booking->id_jadwal_kelas);
        if (! $jadwal) {
            throw new BusinessException('Jadwal kelas tidak ditemukan', 404);
        }

        $now = Carbon::now();
        $jamMulai = Carbon::parse($jadwal->jam_mulai);
        $jamSelesai = Carbon::parse($jadwal->jam_selesai);

        if ($now->isBefore($jamMulai)) {
            throw new BusinessException('Kelas belum dimulai. Absensi hanya bisa dilakukan saat kelas berlangsung', 422);
        }

        if ($now->isAfter($jamSelesai)) {
            throw new BusinessException('Kelas sudah berakhir. Absensi hanya bisa dilakukan saat kelas berlangsung', 422);
        }

        return $this->repository->createOrUpdate($idBooking, $statusKehadiran);
    }
}
