<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\KelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasAdminController extends Controller
{
    public function __construct(protected KelasService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
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
            'nama_kelas' => 'sometimes|string|max:100',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'sometimes|integer|min:1',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Kelas berhasil dihapus');
    }
}
