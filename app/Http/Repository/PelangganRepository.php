<?php

namespace App\Http\Repository;

use App\Domain\Entity\Pelanggan;
use Illuminate\Database\Eloquent\Collection;

class PelangganRepository
{
    public function all(): Collection
    {
        return Pelanggan::with('user')->orderBy('id_pelanggan')->get();
    }

    public function findById(int $id): ?Pelanggan
    {
        return Pelanggan::with('user')->find($id);
    }

    public function findByUserId(int $idUser): ?Pelanggan
    {
        return Pelanggan::where('id_user', $idUser)->first();
    }

    public function create(array $data): Pelanggan
    {
        return Pelanggan::create($data);
    }

    public function update(Pelanggan $pelanggan, array $data): Pelanggan
    {
        $pelanggan->update($data);
        return $pelanggan->fresh();
    }

    public function delete(Pelanggan $pelanggan): void
    {
        $pelanggan->delete();
    }
}
