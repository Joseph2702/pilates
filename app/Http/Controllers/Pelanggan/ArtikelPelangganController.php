<?php

namespace App\Http\Controllers\Pelanggan;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\ArtikelService;
use Illuminate\Http\JsonResponse;

class ArtikelPelangganController extends Controller
{
    public function __construct(protected ArtikelService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->listPublished());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }
}
