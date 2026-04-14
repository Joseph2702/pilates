<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\MutasiKredit;
use App\Http\Repository\MutasiKreditRepository;

/**
 * Single source of truth for kredit balance changes. Every mutation MUST
 * go through here so we always have a matching `mutasi_kredit` ledger row —
 * never decrement `pembelian_package.sisa_kredit` directly elsewhere.
 */
class CreditService
{
    public function __construct(protected MutasiKreditRepository $mutasi) {}

    public function getSaldo(int $idPelanggan): int
    {
        return $this->mutasi->totalSaldo($idPelanggan);
    }

    public function credit(
        int $idPelanggan,
        int $jumlah,
        string $sumber,
        ?int $idReferensi = null,
        ?string $keterangan = null,
    ): MutasiKredit {
        return $this->mutasi->create([
            'id_pelanggan'  => $idPelanggan,
            'jenis_mutasi'  => 'credit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi'  => $idReferensi,
            'keterangan'    => $keterangan,
        ]);
    }

    public function debit(
        int $idPelanggan,
        int $jumlah,
        string $sumber,
        ?int $idReferensi = null,
        ?string $keterangan = null,
    ): MutasiKredit {
        if ($this->getSaldo($idPelanggan) < $jumlah) {
            throw new BusinessException('Insufficient credit balance', 422);
        }

        return $this->mutasi->create([
            'id_pelanggan'  => $idPelanggan,
            'jenis_mutasi'  => 'debit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi'  => $idReferensi,
            'keterangan'    => $keterangan,
        ]);
    }
}
