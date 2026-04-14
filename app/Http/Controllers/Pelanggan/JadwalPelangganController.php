<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\JadwalKelasService;
use Illuminate\Http\JsonResponse;

class JadwalPelangganController extends Controller
{
    public function __construct(protected JadwalKelasService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listUpcoming());
    }

    public function show(int $id): JsonResponse
    {
        $jadwal = $this->service->getOrFail($id);

        return ApiResponse::success($jadwal->load(['kelas', 'instruktur.user']));
    }
}
