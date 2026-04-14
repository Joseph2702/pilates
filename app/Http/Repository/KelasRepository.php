<?php

namespace App\Http\Repository;

use App\Domain\Entity\Kelas;
use Illuminate\Database\Eloquent\Collection;

class KelasRepository
{
    public function all(): Collection
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    public function findById(int $id): ?Kelas
    {
        return Kelas::find($id);
    }

    public function create(array $data): Kelas
    {
        return Kelas::create($data);
    }

    public function update(Kelas $kelas, array $data): Kelas
    {
        $kelas->update($data);
        return $kelas->fresh();
    }

    public function delete(Kelas $kelas): void
    {
        $kelas->delete();
    }
}
