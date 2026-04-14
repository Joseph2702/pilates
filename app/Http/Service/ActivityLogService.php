<?php

namespace App\Http\Service;

use App\Domain\Entity\ActivityLog;
use App\Http\Repository\ActivityLogRepository;
use Illuminate\Database\Eloquent\Collection;

class ActivityLogService
{
    public function __construct(protected ActivityLogRepository $repository) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function listByUser(int $idUser): Collection
    {
        return $this->repository->findByUser($idUser);
    }

    public function log(int $idUser, string $modul, string $aktivitas, ?string $keterangan = null): ActivityLog
    {
        return $this->repository->create([
            'id_user' => $idUser,
            'modul' => $modul,
            'aktivitas' => $aktivitas,
            'keterangan' => $keterangan,
            'tanggal_log' => now(),
        ]);
    }
}
