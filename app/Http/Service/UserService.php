<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\User;
use App\Http\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected UserRepository $userRepo,
        protected ActivityLogService $activityLog,
    ) {}

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
        $result = $this->userRepo->update($user, $data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'user',
            'update',
            'Mengupdate user: '.($data['nama'] ?? $user->nama)
        );

        return $result;
    }

    public function syncRoles(int $idUser, array $roleIds): User
    {
        $user = $this->getOrFail($idUser);
        $user->roles()->sync($roleIds);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'user',
            'sync_roles',
            'Mengubah role untuk user: '.$user->nama
        );

        return $user->fresh('roles');
    }

    public function deactivate(int $id): User
    {
        $user = $this->getOrFail($id);
        $result = $this->userRepo->update($user, ['status' => 'inactive']);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'user',
            'deactivate',
            'Menonaktifkan user: '.$user->nama
        );

        return $result;
    }
}
