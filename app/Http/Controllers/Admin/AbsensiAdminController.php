<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbsensiAdminController extends Controller
{
    public function __construct(protected AbsensiService $service) {}

    public function listByJadwal(int $idJadwalKelas): JsonResponse
    {
        return ApiResponse::success($this->service->listByJadwal($idJadwalKelas));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_booking' => 'required|integer|exists:booking,id_booking',
            'status_kehadiran' => 'required|string|in:hadir,tidak_hadir',
        ]);

        $absensi = $this->service->markAttendance($data['id_booking'], $data['status_kehadiran']);

        return ApiResponse::success($absensi);
    }
}
