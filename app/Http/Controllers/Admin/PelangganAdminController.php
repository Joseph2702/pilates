<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\PelangganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelangganAdminController extends Controller
{
    public function __construct(protected PelangganService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'tanggal_daftar' => 'sometimes|date',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Pelanggan berhasil dihapus');
    }
}
