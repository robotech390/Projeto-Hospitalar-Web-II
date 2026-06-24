<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Realiza login no serviço de autenticação (Equipe 1) e retorna o token JWT.
     *
     * @param string $email
     * @param string $password
     * @return string|null
     */
    public function login(string $email, string $password): ?string
    {
        $api = config('services.microservices.auth');

        if (empty($api)) {
            Log::warning('AuthService: auth microservice URL not configured (services.microservices.auth)');
            return null;
        }

        try {
            $response = Http::acceptJson()->timeout(5)->post(rtrim($api, '/') . '/api/auth/login', [
                'email' => $email,
                'senha' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? $data['access_token'] ?? null;
            }

            Log::error('AuthService: login failed with status ' . $response->status() . ' - ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('AuthService: error during login - ' . $e->getMessage());
            return null;
        }
    }
}
