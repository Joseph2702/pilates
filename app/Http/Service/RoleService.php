<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Role;
use App\Http\Repository\RoleRepository;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(protected RoleRepository $repository) {}

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

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->getOrFail($id);
        return $this->repository->update($role, $data);
    }

    public function delete(int $id): void
    {
        $role = $this->getOrFail($id);
        $this->repository->delete($role);
    }

    public function syncPermissions(int $idRole, array $permissionIds): Role
    {
        $role = $this->getOrFail($idRole);
        $role->permissions()->sync($permissionIds);
        return $role->fresh('permissions');
    }
}
