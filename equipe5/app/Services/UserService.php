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
        // Mock de usuários para quando a API externa não estiver disponível
        $mockUsers = [
            1 => ['nome' => 'Admin Laboratório', 'email' => 'admin@hospital.com', 'perfil' => 'ADMIN'],
            2 => ['nome' => 'Técnico LAB 01', 'email' => 'tecnico1@hospital.com', 'perfil' => 'TECNICO'],
        ];

        try {
            //TODO: Api do grupo 1
            $apiUrl = config('services.microservices.users');
            
            if ($apiUrl) {
                $response = Http::get("{$apiUrl}/usuarios/{$userId}");

                if ($response->successful()) {
                    return $response->json();
                }
            }

            return $mockUsers[($userId % 2) + 1];
        } catch (\Exception $e) {
            Log::error("Erro na integração com API de Usuários: " . $e->getMessage());
            return $mockUsers[($userId % 2) + 1];
        }
    }
}
