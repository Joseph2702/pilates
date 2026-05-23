<?php

namespace App\Http\Controllers;

use App\Common\Exception\BusinessException;
use App\Common\Response\ApiResponse;
use App\Http\Service\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    /**
     * Midtrans HTTP notification endpoint. Must remain unauthenticated.
     */
    public function midtrans(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        if (empty($payload)) {
            return ApiResponse::error('Empty payload', 400);
        }

        // Verify Midtrans signature
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey   = config('midtrans.server_key');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $incomingSignature = $payload['signature_key'] ?? '';

        if ($incomingSignature !== $expectedSignature) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $orderId]);
            return ApiResponse::error('Invalid signature', 403);
        }

        try {
            $this->payments->handleNotification($payload);
        } catch (BusinessException $e) {
            // Known cases (test order_id, already processed, etc.) — return 200 so Midtrans does not retry
            Log::warning('Midtrans webhook skipped: ' . $e->getMessage(), ['order_id' => $orderId]);
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage(), ['payload' => $payload]);
            return ApiResponse::error('Internal error', 500);
        }

        return ApiResponse::success(null, 'OK');
    }
}
