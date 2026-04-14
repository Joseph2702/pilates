<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\PelangganService;
use App\Http\Service\PembelianPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembelianPelangganController extends Controller
{
    public function __construct(
        protected PembelianPackageService $pembelianService,
        protected PelangganService $pelangganService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);

        return ApiResponse::success($this->pembelianService->listByPelanggan($pelanggan->id_pelanggan));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);
        $pembelian = $this->pembelianService->getOrFail($id);

        if ($pembelian->id_pelanggan !== $pelanggan->id_pelanggan) {
            return ApiResponse::notFound('Pembelian tidak ditemukan');
        }

        return ApiResponse::success($pembelian);
    }
}
