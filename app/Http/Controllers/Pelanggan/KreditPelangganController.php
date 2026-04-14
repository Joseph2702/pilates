<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Domain\Entity\MutasiKredit;
use App\Http\Controllers\Controller;
use App\Http\Service\CreditService;
use App\Http\Service\PelangganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KreditPelangganController extends Controller
{
    public function __construct(
        protected CreditService $creditService,
        protected PelangganService $pelangganService,
    ) {}

    public function saldo(Request $request): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);
        $saldo = $this->creditService->getSaldo($pelanggan->id_pelanggan);

        return ApiResponse::success(['saldo_kredit' => $saldo]);
    }

    public function history(Request $request): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);

        $history = MutasiKredit::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('tanggal_mutasi', 'desc')
            ->get();

        return ApiResponse::success($history);
    }
}
