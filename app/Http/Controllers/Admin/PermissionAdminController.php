<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\PermissionRepository;
use App\Http\Service\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionAdminController extends Controller
{
    public function __construct(
        protected PermissionRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

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

        $permission = $this->repository->create($data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'permission',
            'create',
            'Membuat permission baru: '.$data['nama_permission']
        );

        return ApiResponse::created($permission);
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

        $result = $this->repository->update($permission, $data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'permission',
            'update',
            'Mengupdate permission: '.($data['nama_permission'] ?? $permission->nama_permission)
        );

        return ApiResponse::success($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $permission = $this->repository->findById($id);

        if (! $permission) {
            return ApiResponse::notFound('Permission tidak ditemukan');
        }

        $this->repository->delete($permission);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'permission',
            'delete',
            'Menghapus permission: '.$permission->nama_permission
        );

        return ApiResponse::success(null, 'Permission berhasil dihapus');
    }
}
