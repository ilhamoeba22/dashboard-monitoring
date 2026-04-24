<?php

namespace App\Providers;

use App\Services\Mci\MciConnectionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register MCI Connection Service sebagai singleton
        $this->app->singleton('mci.connection', function ($app) {
            return new MciConnectionService();
        });

        // Bind interface ke implementasi (untuk Dependency Injection)
        $this->app->bind(MciConnectionService::class, function ($app) {
            return $app->make('mci.connection');
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