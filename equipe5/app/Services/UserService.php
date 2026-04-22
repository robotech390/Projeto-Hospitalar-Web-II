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
    public function getUserData(int $userId): ?array
    {
        try {
            //TODO: Api do grupo 1
            $apiUrl = config();
            
            $response = Http::get("{$apiUrl}/usuarios/{$userId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Falha ao buscar usuário {$userId} na API externa.");
            return null;
        } catch (\Exception $e) {
            Log::error("Erro na integração com API de Usuários: " . $e->getMessage());
            return null;
        }
    }
}
