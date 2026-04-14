<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Transaksi;
use App\Http\Repository\TransaksiRepository;
use Illuminate\Database\Eloquent\Collection;

class TransaksiService
{
    public function __construct(protected TransaksiRepository $repository) {}

    public function list(): Collection
    {
        return Transaksi::with('pembelianPackage.pelanggan.user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrFail(int $id): Transaksi
    {
        $transaksi = Transaksi::with('pembelianPackage.pelanggan.user')->find($id);

        if (! $transaksi) {
            throw new BusinessException('Transaksi tidak ditemukan', 404);
        }

        return $transaksi;
    }

    public function listByPelanggan(int $idPelanggan): Collection
    {
        return Transaksi::with('pembelianPackage')
            ->whereHas('pembelianPackage', fn ($q) => $q->where('id_pelanggan', $idPelanggan))
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
