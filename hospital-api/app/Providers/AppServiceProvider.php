<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Em produção, força a geração de URLs com HTTPS.
        // Necessário porque o Railway termina o HTTPS no proxy externo
        // e conversa com a aplicação via HTTP interno.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}