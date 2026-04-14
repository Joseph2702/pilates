<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Kelas;
use App\Http\Repository\KelasRepository;
use Illuminate\Database\Eloquent\Collection;

class KelasService
{
    public function __construct(protected KelasRepository $repository) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function getOrFail(int $id): Kelas
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Kelas tidak ditemukan', 404);
    }

    public function create(array $data): Kelas
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Kelas
    {
        $kelas = $this->getOrFail($id);
        return $this->repository->update($kelas, $data);
    }

    public function delete(int $id): void
    {
        $kelas = $this->getOrFail($id);
        $this->repository->delete($kelas);
    }
}
