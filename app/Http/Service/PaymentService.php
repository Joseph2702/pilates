<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\PembelianPackage;
use App\Domain\Entity\Transaksi;
use App\Domain\Enums\PaymentStatus;
use App\Http\Midtrans\MidtransClient;
use App\Http\Repository\PaymentLogRepository;
use App\Http\Repository\PembelianPackageRepository;
use App\Http\Repository\TransaksiRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected PembelianPackageRepository $pembelian,
        protected TransaksiRepository $transaksi,
        protected PaymentLogRepository $paymentLog,
        protected PackageService $packageService,
        protected CreditService $credit,
        protected MidtransClient $midtrans,
    ) {}

    /**
     * Create a pembelian_package + transaksi pair and return the Snap token.
     * No credit is granted yet — that happens in handleNotification() once
     * Midtrans confirms settlement.
     */
    public function checkout(int $idPelanggan, int $idPackage): array
    {
        return $this->checkoutWithPromo($idPelanggan, $idPackage);
    }

    /**
     * Checkout with optional promo discount applied.
     */
    public function checkoutWithPromo(
        int $idPelanggan,
        int $idPackage,
        ?int $idPromo = null,
        float $diskon = 0,
        ?float $hargaAkhir = null,
    ): array {
        $package = $this->packageService->getOrFail($idPackage);

        return DB::transaction(function () use ($idPelanggan, $package, $idPromo, $diskon, $hargaAkhir) {
            $hargaAwal  = (float) $package->harga;
            $hargaAkhir ??= $hargaAwal;

            $pembelian = $this->pembelian->create([
                'id_pelanggan'      => $idPelanggan,
                'id_package'        => $package->id_package,
                'id_promo'          => $idPromo,
                'harga_awal'        => $hargaAwal,
                'diskon'            => $diskon,
                'harga_akhir'       => $hargaAkhir,
                'status_pembelian'  => PaymentStatus::PENDING->value,
                'kredit_earned'    => $package->jumlah_kredit,
                'sisa_kredit'      => $package->jumlah_kredit,
            ]);

            $orderId = 'PIL-'.$pembelian->id_pembelian_package.'-'.Str::upper(Str::random(6));

            $snapToken = $this->midtrans->createSnapToken([
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $hargaAkhir,
                ],
                'item_details' => [[
                    'id'       => (string) $package->id_package,
                    'price'    => (int) $hargaAkhir,
                    'quantity' => 1,
                    'name'     => $package->nama_package,
                ]],
                'callbacks' => [
                    'finish' => route('profile.packages'),
                ],
            ]);

            $this->transaksi->create([
                'order_id'              => $orderId,
                'id_pembelian_package'  => $pembelian->id_pembelian_package,
                'jumlah_bayar'          => $hargaAkhir,
                'snap_token'            => $snapToken,
                'status_internal'       => PaymentStatus::PENDING->value,
            ]);

            return [
                'order_id'   => $orderId,
                'snap_token' => $snapToken,
            ];
        });
    }

    /**
     * Webhook handler. Called by WebhookController after parsing the
     * Midtrans Notification. Idempotent on settlement.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleNotification(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        if (! $orderId) {
            throw new BusinessException('order_id missing in webhook payload', 422);
        }

        $this->paymentLog->record($orderId, $payload);

        $transaksi = $this->transaksi->findByOrderId($orderId);
        if (! $transaksi) {
            throw new BusinessException('Transaksi tidak ditemukan untuk order '.$orderId, 404);
        }

        $internal = $this->mapMidtransStatus($payload);

        DB::transaction(function () use ($transaksi, $payload, $internal) {
            $this->transaksi->update($transaksi, [
                'transaction_status' => $payload['transaction_status'] ?? null,
                'fraud_status'       => $payload['fraud_status'] ?? null,
                'payment_type'       => $payload['payment_type'] ?? null,
                'payment_response'   => $payload,
                'midtrans_order_id'  => $payload['transaction_id'] ?? null,
                'status_internal'    => $internal->value,
            ]);

            $pembelian = $this->pembelian->findById((int) $transaksi->id_pembelian_package);
            if (! $pembelian) {
                return;
            }

            // Idempotency guard: only grant kredit on first successful settlement
            if (
                $internal === PaymentStatus::PAID
                && $pembelian->status_pembelian !== PaymentStatus::PAID->value
            ) {
                $masaBerlaku = $pembelian->package?->masa_berlaku ?? 30;
                $this->pembelian->update($pembelian, [
                    'status_pembelian'   => PaymentStatus::PAID->value,
                    'tanggal_kadaluarsa' => now()->addDays($masaBerlaku),
                ]);

                $this->credit->credit(
                    idPelanggan: (int) $pembelian->id_pelanggan,
                    jumlah: (int) $pembelian->kredit_earned,
                    sumber: 'pembelian_package',
                    idReferensi: (int) $pembelian->id_pembelian_package,
                    keterangan: 'Pembelian package #'.$pembelian->id_pembelian_package,
                );
            } elseif (in_array($internal, [PaymentStatus::FAILED, PaymentStatus::EXPIRED, PaymentStatus::CANCELED], true)) {
                $this->pembelian->update($pembelian, [
                    'status_pembelian' => $internal->value,
                ]);
            }
        });
    }

    /** @param  array<string, mixed>  $payload */
    protected function mapMidtransStatus(array $payload): PaymentStatus
    {
        $status = $payload['transaction_status'] ?? null;
        $fraud  = $payload['fraud_status'] ?? null;

        return match ($status) {
            'capture'  => $fraud === 'accept' ? PaymentStatus::PAID : PaymentStatus::PENDING,
            'settlement' => PaymentStatus::PAID,
            'pending'  => PaymentStatus::PENDING,
            'deny'     => PaymentStatus::FAILED,
            'expire'   => PaymentStatus::EXPIRED,
            'cancel'   => PaymentStatus::CANCELED,
            default    => PaymentStatus::PENDING,
        };
    }
}
