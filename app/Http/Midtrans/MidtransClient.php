<?php

namespace App\Http\Midtrans;

use Midtrans\Notification;
use Midtrans\Snap;

/**
 * Thin wrapper around the Midtrans SDK so the Service layer never imports
 * `Midtrans\*` directly. Keeps Infrastructure swappable + mockable in tests.
 */
class MidtransClient
{
    public function __construct(protected MidtransConfig $config)
    {
        $this->config->boot();
    }

    /** @param  array<string, mixed>  $payload */
    public function createSnapToken(array $payload): string
    {
        return Snap::getSnapToken($payload);
    }

    /**
     * Same as above, but returns the full response object (token + redirect_url).
     *
     * @param  array<string, mixed>  $payload
     * @return object{token: string, redirect_url: string}
     */
    public function createSnapTransaction(array $payload): object
    {
        return Snap::createTransaction($payload);
    }

    /**
     * Parses the inbound webhook body via the SDK's Notification class
     * (reads php://input + verifies signature against the server key).
     */
    public function parseNotification(): Notification
    {
        return new Notification();
    }
}
