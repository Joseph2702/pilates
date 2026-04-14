<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Absensi;
use App\Http\Repository\AbsensiRepository;
use App\Http\Repository\BookingRepository;
use Illuminate\Database\Eloquent\Collection;

class AbsensiService
{
    public function __construct(
        protected AbsensiRepository $repository,
        protected BookingRepository $bookingRepo,
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

        return $this->repository->createOrUpdate($idBooking, $statusKehadiran);
    }
}
