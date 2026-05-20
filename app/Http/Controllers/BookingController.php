<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookings) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_pelanggan'    => ['required', 'integer', 'exists:pelanggan,id_pelanggan'],
            'id_jadwal_kelas' => ['required', 'integer', 'exists:jadwal_kelas,id_jadwal_kelas'],
            'kredit_cost'     => ['nullable', 'integer', 'min:1'],
        ]);

        $booking = $this->bookings->book(
            idPelanggan: (int) $data['id_pelanggan'],
            idJadwalKelas: (int) $data['id_jadwal_kelas'],
            kreditCost: (int) ($data['kredit_cost'] ?? 1),
        );

        return ApiResponse::created($booking, 'Booking berhasil');
    }

    public function cancel(int $id): JsonResponse
    {
        $result = $this->bookings->cancel($id);

        $message = $result['credit_refunded']
            ? 'Booking dibatalkan, kredit dikembalikan'
            : 'Booking dibatalkan, kredit tidak dikembalikan (pembatalan kurang dari 24 jam sebelum kelas)';

        return ApiResponse::success($result['booking'], $message);
    }
}
