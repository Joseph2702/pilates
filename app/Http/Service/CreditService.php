<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\MutasiKredit;
use App\Http\Repository\MutasiKreditRepository;
use App\Http\Repository\PembelianPackageRepository;

/**
 * Single source of truth for kredit balance changes. Every mutation MUST
 * go through here so we always have a matching `mutasi_kredit` ledger row —
 * never decrement `pembelian_package.sisa_kredit` directly elsewhere.
 */
class CreditService
{
    public function __construct(
        protected MutasiKreditRepository $mutasi,
        protected PembelianPackageRepository $pembelian,
    ) {}

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
            'id_pelanggan' => $idPelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi' => $idReferensi,
            'keterangan' => $keterangan,
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
            'id_pelanggan' => $idPelanggan,
            'jenis_mutasi' => 'debit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi' => $idReferensi,
            'keterangan' => $keterangan,
        ]);
    }

    /**
     * Debit kredit FIFO: tulis ke ledger, lalu sinkronisasi sisa_kredit semua package.
     * Harus dipanggil di dalam DB transaction.
     */
    public function debitFromPackages(
        int $idPelanggan,
        int $jumlah,
        string $sumber,
        ?int $idReferensi = null,
        ?string $keterangan = null,
    ): MutasiKredit {
        if ($this->getSaldo($idPelanggan) < $jumlah) {
            throw new BusinessException('Kredit tidak mencukupi', 422);
        }

        $mutasi = $this->mutasi->create([
            'id_pelanggan' => $idPelanggan,
            'jenis_mutasi' => 'debit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi' => $idReferensi,
            'keterangan' => $keterangan,
        ]);

        $this->syncSisaKreditFIFO($idPelanggan);

        return $mutasi;
    }

    /**
     * Kembalikan kredit (refund): tulis ke ledger, lalu sinkronisasi sisa_kredit semua package.
     * Harus dipanggil di dalam DB transaction.
     */
    public function refundToPackage(
        int $idPelanggan,
        int $jumlah,
        string $sumber,
        ?int $idReferensi = null,
        ?string $keterangan = null,
    ): MutasiKredit {
        $mutasi = $this->mutasi->create([
            'id_pelanggan' => $idPelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => $jumlah,
            'sumber_mutasi' => $sumber,
            'id_referensi' => $idReferensi,
            'keterangan' => $keterangan,
        ]);

        $this->syncSisaKreditFIFO($idPelanggan);

        return $mutasi;
    }

    /**
     * Hitung ulang sisa_kredit semua package berdasarkan saldo ledger (FIFO).
     *
     * Algoritma:
     *  - Saldo dari mutasi_kredit = sisa kredit yang benar-benar tersisa
     *  - netConsumed = total kredit yang pernah didapat - saldo saat ini
     *  - Distribusikan netConsumed ke package dari yang terlama (FIFO)
     *
     * Ini otomatis memperbaiki data lama yang stale, refund, maupun booking baru.
     */
    private function syncSisaKreditFIFO(int $idPelanggan): void
    {
        $packages = $this->pembelian->findAllPaidForPelanggan($idPelanggan);

        $saldo = $this->getSaldo($idPelanggan);
        $totalEarned = (int) $packages->sum('kredit_earned');
        $netConsumed = max(0, $totalEarned - $saldo);

        $remaining = $netConsumed;
        foreach ($packages as $package) {
            $used = min((int) $package->kredit_earned, $remaining);
            $newSisaKredit = (int) $package->kredit_earned - $used;
            $package->update(['sisa_kredit' => $newSisaKredit]);
            $remaining -= $used;
        }
    }
}
