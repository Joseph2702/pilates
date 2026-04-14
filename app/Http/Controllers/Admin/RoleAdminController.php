<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleAdminController extends Controller
{
    public function __construct(protected RoleService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
            'is_active' => 'sometimes|boolean',
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
            'nama_role' => 'sometimes|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Role berhasil dihapus');
    }

    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer|exists:permissions,id_permission',
        ]);

        $role = $this->service->syncPermissions($id, $data['permission_ids']);

        return ApiResponse::success($role);
    }
}
