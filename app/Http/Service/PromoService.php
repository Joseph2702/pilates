<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Promo;
use App\Http\Repository\PromoRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PromoService
{
    public function __construct(
        protected PromoRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

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
        $promo = $this->repository->create($data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'promo',
            'create',
            'Membuat promo baru: '.$data['nama_promo']
        );

        return $promo;
    }

    public function update(int $id, array $data): Promo
    {
        $promo = $this->getOrFail($id);
        $result = $this->repository->update($promo, $data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'promo',
            'update',
            'Mengupdate promo: '.($data['nama_promo'] ?? $promo->nama_promo)
        );

        return $result;
    }

    public function delete(int $id): void
    {
        $promo = $this->getOrFail($id);
        $promoName = $promo->nama_promo;
        $this->repository->delete($promo);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'promo',
            'delete',
            'Menghapus promo: '.$promoName
        );
    }
}
