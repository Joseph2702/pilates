<?php

namespace App\Http\Repository;

use App\Domain\Entity\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    public function all(): Collection
    {
        return Role::with('permissions')->orderBy('id_role')->get();
    }

    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return Role::where('nama_role', $name)->first();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
