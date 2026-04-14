<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\JadwalKelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalKelasAdminController extends Controller
{
    public function __construct(protected JadwalKelasService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listUpcoming());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
            'id_instruktur' => 'required|integer|exists:instruktur,id_instruktur',
            'tanggal_kelas' => 'required|date',
            'jam_mulai' => 'required|date',
            'jam_selesai' => 'required|date|after:jam_mulai',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        return ApiResponse::created($this->service->create($data));
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'id_kelas' => 'sometimes|integer|exists:kelas,id_kelas',
            'id_instruktur' => 'sometimes|integer|exists:instruktur,id_instruktur',
            'tanggal_kelas' => 'sometimes|date',
            'jam_mulai' => 'sometimes|date',
            'jam_selesai' => 'sometimes|date|after:jam_mulai',
            'kuota_maksimal' => 'sometimes|integer|min:1',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Jadwal kelas berhasil dihapus');
    }
}
