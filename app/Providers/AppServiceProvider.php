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
            \App\Http\Controllers\Api\ConsultaController::class
        );
        $this->app->bind(
            \App\Http\Controllers\Api\DiagnosticoController::class
        );
        $this->app->bind(
            \App\Http\Controllers\Api\ReceitaController::class
        );
        $this->app->bind(
            \App\Http\Controllers\Api\SolicitacaoExameController::class
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
