<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\PembelianPackage;
use Illuminate\Database\Eloquent\Collection;

class PembelianPackageService
{
    public function list(): Collection
    {
        return PembelianPackage::with(['pelanggan.user', 'package', 'promo'])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
    }

    public function getOrFail(int $id): PembelianPackage
    {
        $pembelian = PembelianPackage::with(['pelanggan.user', 'package', 'promo', 'transaksi'])->find($id);

        if (! $pembelian) {
            throw new BusinessException('Pembelian package tidak ditemukan', 404);
        }

        return $pembelian;
    }

    public function listByPelanggan(int $idPelanggan): Collection
    {
        return PembelianPackage::with(['package', 'promo'])
            ->where('id_pelanggan', $idPelanggan)
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
    }
}
