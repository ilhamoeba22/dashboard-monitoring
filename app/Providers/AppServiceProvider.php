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
        $this->app->singleton(\App\Services\Mci\MciConnectionService::class);
        $this->app->bind(\App\Repositories\Interfaces\FinancingRepositoryInterface::class, \App\Repositories\Mci\FinancingRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\CifRepositoryInterface::class, \App\Repositories\Mci\CifRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\SavingRepositoryInterface::class, \App\Repositories\Mci\SavingRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\DepositRepositoryInterface::class, \App\Repositories\Mci\DepositRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\ReportingRepositoryInterface::class, \App\Repositories\Mci\ReportingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}