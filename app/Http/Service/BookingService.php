<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Booking;
use App\Domain\Enums\BookingStatus;
use App\Http\Repository\BookingRepository;
use App\Http\Repository\JadwalKelasRepository;
use App\Http\Service\CreditService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookings,
        protected JadwalKelasRepository $jadwal,
        protected CreditService $credit,
    ) {}

    /**
     * Atomic booking flow:
     *  1. SELECT ... FOR UPDATE on jadwal_kelas
     *  2. capacity check
     *  3. debit kredit (writes mutasi_kredit)
     *  4. insert booking
     *  5. increment kuota_terisi
     */
    public function book(int $idPelanggan, int $idJadwalKelas, int $kreditCost = 1): Booking
    {
        return DB::transaction(function () use ($idPelanggan, $idJadwalKelas, $kreditCost) {
            $jadwal = $this->jadwal->findForBooking($idJadwalKelas);

            if (! $jadwal) {
                throw new BusinessException('Jadwal tidak ditemukan', 404);
            }

            // Check if class has already started
            if ($jadwal->jam_mulai && Carbon::parse($jadwal->jam_mulai)->isPast()) {
                throw new BusinessException('Kelas sudah dimulai, tidak bisa booking lagi', 422);
            }

            if ((int) $jadwal->kuota_terisi >= (int) $jadwal->kuota_maksimal) {
                throw new BusinessException('Jadwal sudah penuh', 422);
            }

            // Prevent duplicate booking for the same class
            $alreadyBooked = Booking::where('id_pelanggan', $idPelanggan)
                ->where('id_jadwal_kelas', $idJadwalKelas)
                ->where('status_booking', '!=', BookingStatus::CANCELED->value)
                ->exists();

            if ($alreadyBooked) {
                throw new BusinessException('Anda sudah memesan kelas ini', 422);
            }

            $this->credit->debit(
                idPelanggan: $idPelanggan,
                jumlah: $kreditCost,
                sumber: 'booking',
                idReferensi: $idJadwalKelas,
                keterangan: 'Booking jadwal #'.$idJadwalKelas,
            );

            $booking = $this->bookings->create([
                'id_pelanggan'    => $idPelanggan,
                'id_jadwal_kelas' => $idJadwalKelas,
                'status_booking'  => BookingStatus::BOOKED->value,
            ]);

            $this->jadwal->incrementKuotaTerisi($jadwal);

            return $booking;
        });
    }

    public function cancel(int $idBooking, int $kreditRefund = 1): array
    {
        return DB::transaction(function () use ($idBooking, $kreditRefund) {
            $booking = $this->bookings->findById($idBooking);

            if (! $booking) {
                throw new BusinessException('Booking tidak ditemukan', 404);
            }

            if ($booking->status_booking === BookingStatus::CANCELED->value) {
                throw new BusinessException('Booking sudah dibatalkan', 422);
            }

            $jadwal = $this->jadwal->findForBooking((int) $booking->id_jadwal_kelas);
            if ($jadwal) {
                $this->jadwal->decrementKuotaTerisi($jadwal);
            }

            // Refund credit only if cancellation is made at least 24 hours before class start
            $classStart = $jadwal && $jadwal->jam_mulai ? Carbon::parse($jadwal->jam_mulai) : null;
            $isRefundable = $classStart && $classStart->gt(Carbon::now()->addHours(24));

            if ($isRefundable) {
                $this->credit->credit(
                    idPelanggan: (int) $booking->id_pelanggan,
                    jumlah: $kreditRefund,
                    sumber: 'booking_refund',
                    idReferensi: $idBooking,
                    keterangan: 'Refund cancel booking #'.$idBooking,
                );
            }

            $booking = $this->bookings->update($booking, [
                'status_booking' => BookingStatus::CANCELED->value,
            ]);

            return ['booking' => $booking, 'credit_refunded' => $isRefundable];
        });
    }
}
