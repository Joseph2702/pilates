<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\JadwalKelas;
use App\Http\Repository\JadwalKelasRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class JadwalKelasService
{
    public function __construct(
        protected JadwalKelasRepository $repository,
        protected ActivityLogService $activityLog,
    ) {}

    public function listUpcoming(): Collection
    {
        return $this->repository->listUpcoming();
    }

    public function getOrFail(int $id): JadwalKelas
    {
        return $this->repository->findById($id)
            ?? throw new BusinessException('Jadwal kelas tidak ditemukan', 404);
    }

    public function create(array $data): JadwalKelas
    {
        $data['kuota_terisi'] = 0;
        $jadwal = JadwalKelas::create($data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'jadwal_kelas',
            'create',
            'Membuat jadwal kelas baru'
        );

        return $jadwal;
    }

    public function update(int $id, array $data): JadwalKelas
    {
        $jadwal = $this->getOrFail($id);
        $jadwal->update($data);

        $this->activityLog->log(
            Auth::id() ?? 0,
            'jadwal_kelas',
            'update',
            'Mengupdate jadwal kelas'
        );

        return $jadwal->fresh();
    }

    public function delete(int $id): void
    {
        $jadwal = $this->getOrFail($id);
        $jadwal->delete();

        $this->activityLog->log(
            Auth::id() ?? 0,
            'jadwal_kelas',
            'delete',
            'Menghapus jadwal kelas'
        );
    }

    public function listByInstruktur(int $idInstruktur): Collection
    {
        return JadwalKelas::with(['kelas', 'bookings.pelanggan.user'])
            ->where('id_instruktur', $idInstruktur)
            ->where('tanggal_kelas', '>=', now())
            ->orderBy('tanggal_kelas')
            ->get();
    }
}
