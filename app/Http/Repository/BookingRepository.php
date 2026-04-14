<?php

namespace App\Http\Repository;

use App\Domain\Entity\Booking;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    /** @param  array<string, mixed>  $data */
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function findById(int $id): ?Booking
    {
        return Booking::find($id);
    }

    /** @return Collection<int, Booking> */
    public function listForPelanggan(int $idPelanggan): Collection
    {
        return Booking::where('id_pelanggan', $idPelanggan)
            ->orderByDesc('tanggal_booking')
            ->get();
    }

    /** @param  array<string, mixed>  $data */
    public function update(Booking $booking, array $data): Booking
    {
        $booking->fill($data)->save();

        return $booking->refresh();
    }
}
