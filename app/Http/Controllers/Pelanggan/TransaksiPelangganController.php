<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\PelangganService;
use App\Http\Service\TransaksiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransaksiPelangganController extends Controller
{
    public function __construct(
        protected TransaksiService $transaksiService,
        protected PelangganService $pelangganService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pelanggan = $this->pelangganService->getByUserId($request->user()->id_user);

        return ApiResponse::success($this->transaksiService->listByPelanggan($pelanggan->id_pelanggan));
    }
}
