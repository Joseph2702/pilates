<?php

namespace App\Http\Repository;

use App\Domain\Entity\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    public function all(): Collection
    {
        return Permission::orderBy('nama_permission')->get();
    }

    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission->fresh();
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }
}
