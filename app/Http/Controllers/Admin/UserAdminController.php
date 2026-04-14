<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function __construct(protected UserService $service) {}

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
            'nama' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|max:100|unique:users,email,'.$id.',id_user',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|string|max:10',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        return ApiResponse::success($this->service->update($id, $data));
    }

    public function deactivate(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->deactivate($id));
    }

    public function syncRoles(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer|exists:roles,id_role',
        ]);

        $user = $this->service->syncRoles($id, $data['role_ids']);

        return ApiResponse::success($user);
    }
}
