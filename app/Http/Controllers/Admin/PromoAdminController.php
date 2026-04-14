<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\PromoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoAdminController extends Controller
{
    public function __construct(protected PromoService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listAll());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode_promo' => 'required|string|max:50',
            'nama_promo' => 'required|string|max:100',
            'persenan_diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_promo' => 'required|string|in:active,inactive',
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
            'kode_promo' => 'sometimes|string|max:50',
            'nama_promo' => 'sometimes|string|max:100',
            'persenan_diskon' => 'sometimes|numeric|min:0|max:100',
            'tanggal_mulai' => 'sometimes|date',
            'tanggal_selesai' => 'sometimes|date|after:tanggal_mulai',
            'status_promo' => 'sometimes|string|in:active,inactive',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Promo berhasil dihapus');
    }
}
