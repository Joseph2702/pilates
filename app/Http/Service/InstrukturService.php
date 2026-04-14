<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Instruktur;
use App\Http\Repository\InstrukturRepository;
use App\Http\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class InstrukturService
{
    public function __construct(
        protected InstrukturRepository $repository,
        protected UserRepository $userRepo,
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function getOrFail(int $id): Instruktur
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Instruktur tidak ditemukan', 404);
    }

    public function create(array $data): Instruktur
    {
        $user = $this->userRepo->findById($data['id_user'])
            ?? throw new BusinessException('User tidak ditemukan', 404);

        $existing = $this->repository->findByUserId($user->id_user);
        if ($existing) {
            throw new BusinessException('User sudah terdaftar sebagai instruktur', 422);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Instruktur
    {
        $instruktur = $this->getOrFail($id);
        return $this->repository->update($instruktur, $data);
    }

    public function delete(int $id): void
    {
        $instruktur = $this->getOrFail($id);
        $this->repository->delete($instruktur);
    }
}
