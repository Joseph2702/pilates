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
            'tanggal_kelas' => 'required|date_format:Y-m-d',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        // Validate that jam_mulai < jam_selesai
        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            return ApiResponse::error('Jam selesai harus lebih besar dari jam mulai', 422);
        }

        // Combine tanggal_kelas + jam_mulai into timestamp
        $data['jam_mulai'] = $data['tanggal_kelas'] . ' ' . $data['jam_mulai'];
        $data['jam_selesai'] = $data['tanggal_kelas'] . ' ' . $data['jam_selesai'];
        $data['tanggal_kelas'] = $data['tanggal_kelas'] . ' 00:00:00';

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
            'tanggal_kelas' => 'sometimes|date_format:Y-m-d',
            'jam_mulai' => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i',
            'kuota_maksimal' => 'sometimes|integer|min:1',
        ]);

        // Validate that jam_mulai < jam_selesai if both are provided
        if (isset($data['jam_mulai']) && isset($data['jam_selesai']) && $data['jam_mulai'] >= $data['jam_selesai']) {
            return ApiResponse::error('Jam selesai harus lebih besar dari jam mulai', 422);
        }

        // Combine tanggal_kelas + jam_mulai into timestamp if date/time fields present
        if (isset($data['tanggal_kelas'])) {
            if (isset($data['jam_mulai'])) {
                $data['jam_mulai'] = $data['tanggal_kelas'] . ' ' . $data['jam_mulai'];
            }
            if (isset($data['jam_selesai'])) {
                $data['jam_selesai'] = $data['tanggal_kelas'] . ' ' . $data['jam_selesai'];
            }
            $data['tanggal_kelas'] = $data['tanggal_kelas'] . ' 00:00:00';
        }

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Jadwal kelas berhasil dihapus');
    }
}
