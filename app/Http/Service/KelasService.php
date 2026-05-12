<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Kelas;
use App\Http\Repository\KelasRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class KelasService
{
    public function __construct(
        protected KelasRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

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
        $kelas = $this->repository->create($data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'kelas',
            'create',
            'Membuat kelas baru: ' . $data['nama_kelas']
        );
        
        return $kelas;
    }

    public function update(int $id, array $data): Kelas
    {
        $kelas = $this->getOrFail($id);
        $result = $this->repository->update($kelas, $data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'kelas',
            'update',
            'Mengupdate kelas: ' . ($data['nama_kelas'] ?? $kelas->nama_kelas)
        );
        
        return $result;
    }

    public function delete(int $id): void
    {
        $kelas = $this->getOrFail($id);
        $kelasName = $kelas->nama_kelas;
        $this->repository->delete($kelas);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'kelas',
            'delete',
            'Menghapus kelas: ' . $kelasName
        );
    }
}
