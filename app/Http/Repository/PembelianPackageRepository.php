<?php

namespace App\Http\Repository;

use App\Domain\Entity\PembelianPackage;

class PembelianPackageRepository
{
    /** @param  array<string, mixed>  $data */
    public function create(array $data): PembelianPackage
    {
        return PembelianPackage::create($data);
    }

    public function findById(int $id): ?PembelianPackage
    {
        return PembelianPackage::with('package')->find($id);
    }

    /** @param  array<string, mixed>  $data */
    public function update(PembelianPackage $pembelian, array $data): PembelianPackage
    {
        $pembelian->fill($data)->save();

        return $pembelian->refresh();
    }
}
