<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Artikel;
use App\Http\Repository\ArtikelRepository;
use Illuminate\Database\Eloquent\Collection;

class ArtikelService
{
    public function __construct(protected ArtikelRepository $repository) {}

    public function listPublished(): Collection
    {
        return $this->repository->listPublished();
    }

    public function listAll(): Collection
    {
        return $this->repository->all();
    }

    public function getOrFail(int $id): Artikel
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Artikel tidak ditemukan', 404);
    }

    public function create(array $data): Artikel
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Artikel
    {
        $artikel = $this->getOrFail($id);
        return $this->repository->update($artikel, $data);
    }

    public function delete(int $id): void
    {
        $artikel = $this->getOrFail($id);
        $this->repository->delete($artikel);
    }
}
