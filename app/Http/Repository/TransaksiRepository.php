<?php

namespace App\Http\Repository;

use App\Domain\Entity\Transaksi;

class TransaksiRepository
{
    /** @param  array<string, mixed>  $data */
    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }

    public function findByOrderId(string $orderId): ?Transaksi
    {
        return Transaksi::where('order_id', $orderId)->first();
    }

    /** @param  array<string, mixed>  $data */
    public function update(Transaksi $transaksi, array $data): Transaksi
    {
        $transaksi->fill($data)->save();

        return $transaksi->refresh();
    }
}
