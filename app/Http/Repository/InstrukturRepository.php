<?php

namespace App\Http\Repository;

use App\Domain\Entity\Instruktur;
use Illuminate\Database\Eloquent\Collection;

class InstrukturRepository
{
    public function all(): Collection
    {
        return Instruktur::with('user')->orderBy('id_instruktur')->get();
    }

    public function findById(int $id): ?Instruktur
    {
        return Instruktur::with('user')->find($id);
    }

    public function findByUserId(int $idUser): ?Instruktur
    {
        return Instruktur::where('id_user', $idUser)->first();
    }

    public function create(array $data): Instruktur
    {
        return Instruktur::create($data);
    }

    public function update(Instruktur $instruktur, array $data): Instruktur
    {
        $instruktur->update($data);
        return $instruktur->fresh();
    }

    public function delete(Instruktur $instruktur): void
    {
        $instruktur->delete();
    }
}
