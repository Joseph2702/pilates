<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\PermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionAdminController extends Controller
{
    public function __construct(protected PermissionRepository $repository) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->repository->all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_permission' => 'required|string|max:100|unique:permissions,nama_permission',
            'deskripsi' => 'nullable|string',
        ]);

        return ApiResponse::created($this->repository->create($data));
    }

    public function show(int $id): JsonResponse
    {
        $permission = $this->repository->findById($id);

        if (! $permission) {
            return ApiResponse::notFound('Permission tidak ditemukan');
        }

        return ApiResponse::success($permission);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $permission = $this->repository->findById($id);

        if (! $permission) {
            return ApiResponse::notFound('Permission tidak ditemukan');
        }

        $data = $request->validate([
            'nama_permission' => 'sometimes|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        return ApiResponse::success($this->repository->update($permission, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $permission = $this->repository->findById($id);

        if (! $permission) {
            return ApiResponse::notFound('Permission tidak ditemukan');
        }

        $this->repository->delete($permission);

        return ApiResponse::success(null, 'Permission berhasil dihapus');
    }
}
