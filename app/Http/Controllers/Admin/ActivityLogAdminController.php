<?php

namespace App\Http\Controllers\Admin;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use Illuminate\Http\JsonResponse;

class ActivityLogAdminController extends Controller
{
    public function __construct(protected ActivityLogService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->list());
    }

    public function byUser(int $idUser): JsonResponse
    {
        return ApiResponse::success($this->service->listByUser($idUser));
    }
}
