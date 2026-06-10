<?php

namespace App\Providers;

use App\Http\Midtrans\MidtransClient;
use App\Http\Midtrans\MidtransConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MidtransConfig::class, function ($app) {
            return new MidtransConfig;
        });

        $this->app->bind(MidtransClient::class, function ($app) {
            return new MidtransClient(
                $app->make(MidtransConfig::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
