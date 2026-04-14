<?php

namespace App\Http\Repository;

use App\Domain\Entity\Artikel;
use Illuminate\Database\Eloquent\Collection;

class ArtikelRepository
{
    public function all(): Collection
    {
        return Artikel::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function listPublished(): Collection
    {
        return Artikel::with('user')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->get();
    }

    public function findById(int $id): ?Artikel
    {
        return Artikel::with('user')->find($id);
    }

    public function create(array $data): Artikel
    {
        return Artikel::create($data);
    }

    public function update(Artikel $artikel, array $data): Artikel
    {
        $artikel->update($data);
        return $artikel->fresh();
    }

    public function delete(Artikel $artikel): void
    {
        $artikel->delete();
    }
}
