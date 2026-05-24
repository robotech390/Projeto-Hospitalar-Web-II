<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Registra um log em um serviço centralizado.
     *
     * @param array $payload
     * @param string|null $token
     * @return bool
     */
    public function create(array $payload, ?string $token = null): bool
    {
        $api = config('services.microservices.logs');

        if (empty($api)) {
            Log::warning('LogService: logs microservice URL not configured (services.microservices.logs)');
            return false;
        }

        try {
            $request = Http::acceptJson()->timeout(5);

            if (empty($token)) {
                $token = app(\App\Services\TokenService::class)->getToken();
            }

            if (! empty($token)) {
                $request = $request->withToken($token);
            }

            $response = $request->post(rtrim($api, '/') . '/api/logs', $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('LogService: failed to create log [' . $response->status() . '] ' . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::er'ror('LogService: error creating log - ' . $e->getMessage());
            return false;
        }
    }
}
