<?php

namespace App\Http\Repository;

use App\Domain\Entity\ActivityLog;
use Illuminate\Database\Eloquent\Collection;

class ActivityLogRepository
{
    public function all(): Collection
    {
        return ActivityLog::with('user')->orderBy('tanggal_log', 'desc')->get();
    }

    public function findByUser(int $idUser): Collection
    {
        return ActivityLog::where('id_user', $idUser)->orderBy('tanggal_log', 'desc')->get();
    }

    public function create(array $data): ActivityLog
    {
        return ActivityLog::create($data);
    }
}
