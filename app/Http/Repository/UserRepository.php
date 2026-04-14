<?php

namespace App\Http\Repository;

use App\Domain\Entity\User;

class UserRepository
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /** @param  array<string, mixed>  $data */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();

        return $user->refresh();
    }
}
