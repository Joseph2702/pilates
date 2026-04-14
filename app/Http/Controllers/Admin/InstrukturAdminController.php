<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\InstrukturService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstrukturAdminController extends Controller
{
    public function __construct(protected InstrukturService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'spesialisasi' => 'nullable|string|max:50',
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
            'spesialisasi' => 'nullable|string|max:50',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Instruktur berhasil dihapus');
    }
}
