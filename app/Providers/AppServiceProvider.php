<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Interfaces\CifRepositoryInterface;
use App\Repositories\Interfaces\DepositRepositoryInterface;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use App\Repositories\Interfaces\FinancingTunggakanRepositoryInterface;
use App\Repositories\Interfaces\ReportingRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Repositories\Mci\CifRepository;
use App\Repositories\Mci\DepositRepository;
use App\Repositories\Mci\FinancingRepository;
use App\Repositories\Mci\FinancingTunggakanRepository;
use App\Repositories\Mci\ReportingRepository;
use App\Repositories\Mci\SavingRepository;
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
            return new MciConnectionService;
        });

        // Bind interface ke implementasi (untuk Dependency Injection)
        $this->app->singleton(MciConnectionService::class);
        $this->app->bind(FinancingRepositoryInterface::class, FinancingRepository::class);
        $this->app->bind(FinancingTunggakanRepositoryInterface::class, FinancingTunggakanRepository::class);
        $this->app->bind(CifRepositoryInterface::class, CifRepository::class);
        $this->app->bind(SavingRepositoryInterface::class, SavingRepository::class);
        $this->app->bind(DepositRepositoryInterface::class, DepositRepository::class);
        $this->app->bind(ReportingRepositoryInterface::class, ReportingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
