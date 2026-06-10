<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Role;
use App\Http\Repository\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RoleService
{
    public function __construct(
        protected RoleRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function getOrFail(int $id): Role
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Role tidak ditemukan', 404);
    }

    public function create(array $data): Role
    {
        $existing = $this->repository->findByName($data['nama_role']);
        if ($existing) {
            throw new BusinessException('Role dengan nama tersebut sudah ada', 422);
        }

        $role = $this->repository->create($data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'role',
            'create',
            'Membuat role baru: '.$data['nama_role']
        );

        return $role;
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->getOrFail($id);
        $result = $this->repository->update($role, $data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'role',
            'update',
            'Mengupdate role: '.($data['nama_role'] ?? $role->nama_role)
        );

        return $result;
    }

    public function delete(int $id): void
    {
        $role = $this->getOrFail($id);
        $roleName = $role->nama_role;
        $this->repository->delete($role);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'role',
            'delete',
            'Menghapus role: '.$roleName
        );
    }

    public function syncPermissions(int $idRole, array $permissionIds): Role
    {
        $role = $this->getOrFail($idRole);
        $role->permissions()->sync($permissionIds);

        return $role->fresh('permissions');
    }
}
