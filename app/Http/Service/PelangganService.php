<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Pelanggan;
use App\Http\Repository\PelangganRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PelangganService
{
    public function __construct(
        protected PelangganRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function getOrFail(int $id): Pelanggan
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Pelanggan tidak ditemukan', 404);
    }

    public function getByUserId(int $idUser): Pelanggan
    {
        return $this->repository->findByUserId($idUser)
            ?? throw new BusinessException('Profil pelanggan tidak ditemukan', 404);
    }

    public function update(int $id, array $data): Pelanggan
    {
        $pelanggan = $this->getOrFail($id);
        $result = $this->repository->update($pelanggan, $data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'pelanggan',
            'update',
            'Mengupdate data pelanggan'
        );
        
        return $result;
    }

    public function delete(int $id): void
    {
        $pelanggan = $this->getOrFail($id);
        $this->repository->delete($pelanggan);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'pelanggan',
            'delete',
            'Menghapus data pelanggan'
        );
    }
}
