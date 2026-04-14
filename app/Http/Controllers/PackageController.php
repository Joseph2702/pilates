<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\PackageService;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    public function __construct(protected PackageService $packages) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->packages->listActive());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->packages->getOrFail($id));
    }
}
