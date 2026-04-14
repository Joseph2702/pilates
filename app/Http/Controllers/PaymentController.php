<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_pelanggan' => ['required', 'integer', 'exists:pelanggan,id_pelanggan'],
            'id_package'   => ['required', 'integer', 'exists:package,id_package'],
        ]);

        $result = $this->payments->checkout(
            idPelanggan: (int) $data['id_pelanggan'],
            idPackage: (int) $data['id_package'],
        );

        return ApiResponse::created($result, 'Snap token berhasil dibuat');
    }
}
