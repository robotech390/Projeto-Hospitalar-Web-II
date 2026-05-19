<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TokenService
{
    /**
     * Retorna um token JWT para autenticação entre serviços.
     * O token é cacheado para evitar logins repetidos.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        $cacheKey = 'microservice_auth_token';

        $token = Cache::get($cacheKey);
        if (! empty($token)) {
            return $token;
        }

        $email = env('SERVICE_AUTH_EMAIL');
        $password = env('SERVICE_AUTH_PASSWORD');

        if (empty($email) || empty($password)) {
            Log::warning('TokenService: service account credentials not configured (SERVICE_AUTH_EMAIL / SERVICE_AUTH_PASSWORD)');
            return null;
        }

        $auth = app(AuthService::class);
        $token = $auth->login($email, $password);

        if (empty($token)) {
            Log::error('TokenService: failed to obtain token from AuthService');
            return null;
        }

        $ttl = intval(env('SERVICE_AUTH_TOKEN_TTL', 3600));
        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }
}
