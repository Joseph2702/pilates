<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\BookingRepository;
use App\Http\Service\PelangganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingPelangganController extends Controller
{
    public function __construct(
        protected BookingRepository $bookingRepo,
        protected PelangganService $pelangganService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);
        $bookings = $this->bookingRepo->listForPelanggan($pelanggan->id_pelanggan);

        return ApiResponse::success($bookings->load('jadwalKelas.kelas'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);
        $booking = $this->bookingRepo->findById($id);

        if (! $booking || $booking->id_pelanggan !== $pelanggan->id_pelanggan) {
            return ApiResponse::notFound('Booking tidak ditemukan');
        }

        return ApiResponse::success($booking->load(['jadwalKelas.kelas', 'absensi']));
    }
}
