<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Artikel;
use App\Http\Repository\ArtikelRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ArtikelService
{
    public function __construct(
        protected ArtikelRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

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
        $artikel = $this->repository->create($data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'artikel',
            'create',
            'Membuat artikel baru: ' . $data['judul_artikel']
        );
        
        return $artikel;
    }

    public function update(int $id, array $data): Artikel
    {
        $artikel = $this->getOrFail($id);
        $result = $this->repository->update($artikel, $data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'artikel',
            'update',
            'Mengupdate artikel: ' . ($data['judul_artikel'] ?? $artikel->judul_artikel)
        );
        
        return $result;
    }

    public function delete(int $id): void
    {
        $artikel = $this->getOrFail($id);
        $judul = $artikel->judul_artikel;
        $this->repository->delete($artikel);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'artikel',
            'delete',
            'Menghapus artikel: ' . $judul
        );
    }
}
