<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Promo;
use App\Http\Repository\PromoRepository;
use Illuminate\Database\Eloquent\Collection;

class PromoService
{
    public function __construct(protected PromoRepository $repository) {}

    public function listAll(): Collection
    {
        return $this->repository->all();
    }

    public function listActive(): Collection
    {
        return $this->repository->listActive();
    }

    public function getOrFail(int $id): Promo
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Promo tidak ditemukan', 404);
    }

    public function create(array $data): Promo
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Promo
    {
        $promo = $this->getOrFail($id);
        return $this->repository->update($promo, $data);
    }

    public function delete(int $id): void
    {
        $promo = $this->getOrFail($id);
        $this->repository->delete($promo);
    }
}
