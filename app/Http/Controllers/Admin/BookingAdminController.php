<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Domain\Entity\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BookingAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['pelanggan.user', 'jadwalKelas.kelas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success($bookings);
    }

    public function show(int $id): JsonResponse
    {
        $booking = Booking::with(['pelanggan.user', 'jadwalKelas.kelas', 'absensi'])->find($id);

        if (! $booking) {
            return ApiResponse::notFound('Booking tidak ditemukan');
        }

        return ApiResponse::success($booking);
    }
}
