<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageAdminController extends Controller
{
    public function __construct(protected PackageService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listActive());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_package' => 'required|string|max:100',
            'jumlah_kredit' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'masa_berlaku' => 'required|integer|min:1',
            'status_package' => 'required|string|in:active,inactive',
        ]);

        $package = $this->service->create($data);

        return ApiResponse::created($package);
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'nama_package' => 'sometimes|string|max:100',
            'jumlah_kredit' => 'sometimes|integer|min:1',
            'harga' => 'sometimes|numeric|min:0',
            'masa_berlaku' => 'sometimes|integer|min:1',
            'status_package' => 'sometimes|string|in:active,inactive',
        ]);

        $package = $this->service->update($id, $data);

        return ApiResponse::success($package);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Package berhasil dihapus');
    }
}
