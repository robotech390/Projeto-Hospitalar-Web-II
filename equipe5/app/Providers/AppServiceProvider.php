<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\LabExamRepositoryInterface::class,
            \App\Repositories\LabExamEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\LabCatalogRepositoryInterface::class,
            \App\Repositories\LabCatalogEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\LabDashboardRepositoryInterface::class,
            \App\Repositories\LabDashboardEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\LabSolicitationRepositoryInterface::class,
            \App\Repositories\LabSolicitationEloquentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
