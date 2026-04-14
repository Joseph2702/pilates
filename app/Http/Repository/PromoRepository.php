<?php

namespace App\Http\Repository;

use App\Domain\Entity\Promo;
use Illuminate\Database\Eloquent\Collection;

class PromoRepository
{
    public function all(): Collection
    {
        return Promo::orderBy('created_at', 'desc')->get();
    }

    public function listActive(): Collection
    {
        return Promo::where('status_promo', 'active')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->get();
    }

    public function findById(int $id): ?Promo
    {
        return Promo::find($id);
    }

    public function findByKode(string $kode): ?Promo
    {
        return Promo::where('kode_promo', $kode)
            ->where('status_promo', 'active')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();
    }

    public function create(array $data): Promo
    {
        return Promo::create($data);
    }

    public function update(Promo $promo, array $data): Promo
    {
        $promo->update($data);
        return $promo->fresh();
    }

    public function delete(Promo $promo): void
    {
        $promo->delete();
    }
}
