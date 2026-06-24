<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Busca dados do usuário em uma API externa.
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserData(int $userId, ?string $token = null): ?array
    {
        $apiUrl = config('services.microservices.users');

        if (empty($apiUrl)) {
            Log::warning('UserService: users microservice URL not configured (services.microservices.users)');
            return null;
        }

        try {
            $request = Http::acceptJson()->timeout(5);

            if (empty($token)) {
                $token = app(\App\Services\TokenService::class)->getToken();
            }

            if (! empty($token)) {
                $request = $request->withToken($token);
            }

            $response = $request->get(rtrim($apiUrl, '/') . "/api/usuarios/{$userId}");

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                return null;
            }

            Log::error("UserService: unexpected response [{$response->status()}] when fetching user {$userId}: " . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('UserService: error fetching user ' . $userId . ' - ' . $e->getMessage());
            return null;
        }
    }
}
