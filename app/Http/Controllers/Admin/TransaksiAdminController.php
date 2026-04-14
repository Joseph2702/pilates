<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\TransaksiService;
use Illuminate\Http\JsonResponse;

class TransaksiAdminController extends Controller
{
    public function __construct(protected TransaksiService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getOrFail($id));
    }
}
