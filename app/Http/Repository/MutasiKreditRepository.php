<?php

namespace App\Http\Repository;

use App\Domain\Entity\MutasiKredit;

class MutasiKreditRepository
{
    /** @param  array<string, mixed>  $data */
    public function create(array $data): MutasiKredit
    {
        return MutasiKredit::create($data);
    }

    /**
     * Computed running balance = sum(credits) - sum(debits) for a pelanggan.
     */
    public function totalSaldo(int $idPelanggan): int
    {
        $credit = (int) MutasiKredit::where('id_pelanggan', $idPelanggan)
            ->where('jenis_mutasi', 'credit')
            ->sum('jumlah_kredit');

        $debit = (int) MutasiKredit::where('id_pelanggan', $idPelanggan)
            ->where('jenis_mutasi', 'debit')
            ->sum('jumlah_kredit');

        return $credit - $debit;
    }
}
