<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\User;
use App\Http\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(protected UserRepository $userRepo) {}

    public function list(): Collection
    {
        return User::with('roles')->orderBy('id_user')->get();
    }

    public function getOrFail(int $id): User
    {
        $user = $this->userRepo->findById($id);
        if (! $user) {
            throw new BusinessException('User tidak ditemukan', 404);
        }

        return $user->load('roles');
    }

    public function update(int $id, array $data): User
    {
        $user = $this->getOrFail($id);
        return $this->userRepo->update($user, $data);
    }

    public function syncRoles(int $idUser, array $roleIds): User
    {
        $user = $this->getOrFail($idUser);
        $user->roles()->sync($roleIds);
        return $user->fresh('roles');
    }

    public function deactivate(int $id): User
    {
        $user = $this->getOrFail($id);
        return $this->userRepo->update($user, ['status' => 'inactive']);
    }
}
