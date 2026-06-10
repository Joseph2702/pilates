<?php

namespace App\Http\Midtrans;

use Midtrans\Config;

/**
 * Applies Midtrans SDK global config from config/midtrans.php once per process.
 * Bind as a singleton; MidtransClient depends on it.
 */
class MidtransConfig
{
    protected bool $booted = false;

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
        Config::$overrideNotifUrl = url('/api/webhook/midtrans');

        $this->booted = true;
    }
}
