<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\ArtikelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtikelAdminController extends Controller
{
    public function __construct(protected ArtikelService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listAll());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'gambar_artikel' => 'nullable|string',
            'konten_artikel' => 'required|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        $data['id_user'] = $request->user()->id_user;

        return ApiResponse::created($this->service->create($data));
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'judul_artikel' => 'sometimes|string|max:255',
            'gambar_artikel' => 'nullable|string',
            'konten_artikel' => 'sometimes|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Artikel berhasil dihapus');
    }
}
