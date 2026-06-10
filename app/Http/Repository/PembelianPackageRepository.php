<?php

namespace App\Http\Repository;

use App\Domain\Entity\PembelianPackage;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Active paid packages with remaining credits, ordered oldest first (FIFO for booking debit).
     *
     * @return Collection<int, PembelianPackage>
     */
    public function findActiveForPelanggan(int $idPelanggan): Collection
    {
        return PembelianPackage::where('id_pelanggan', $idPelanggan)
            ->where('status_pembelian', 'paid')
            ->where('sisa_kredit', '>', 0)
            ->where(fn ($q) => $q->whereNull('tanggal_kadaluarsa')->orWhere('tanggal_kadaluarsa', '>', now()))
            ->orderBy('tanggal_pembelian', 'asc')
            ->get();
    }

    /**
     * Oldest active paid package that has been partially used — used to restore sisa_kredit on refund.
     */
    public function findOldestPartiallyUsed(int $idPelanggan): ?PembelianPackage
    {
        return PembelianPackage::where('id_pelanggan', $idPelanggan)
            ->where('status_pembelian', 'paid')
            ->whereColumn('sisa_kredit', '<', 'kredit_earned')
            ->where(fn ($q) => $q->whereNull('tanggal_kadaluarsa')->orWhere('tanggal_kadaluarsa', '>', now()))
            ->orderBy('tanggal_pembelian', 'asc')
            ->first();
    }

    /**
     * All paid packages ordered by purchase date (for FIFO sisa_kredit sync).
     *
     * @return Collection<int, PembelianPackage>
     */
    public function findAllPaidForPelanggan(int $idPelanggan): Collection
    {
        return PembelianPackage::where('id_pelanggan', $idPelanggan)
            ->where('status_pembelian', 'paid')
            ->orderBy('tanggal_pembelian', 'asc')
            ->get();
    }
}
