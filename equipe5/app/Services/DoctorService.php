<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DoctorService
{
    /**
     * Retorna a URL base da API da Equipe 1.
     */
    protected function getBaseUrl(): string
    {
        return env('API_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Busca dados do médico na API do Grupo 1.
     */
    public function getDoctorData(int $id): ?array
    {
        try {
            $token = app(\App\Services\TokenService::class)->getToken();
            $request = Http::timeout(5);
            
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get($this->getBaseUrl() . '/usuarios', [
                'funcao' => 'medico'
            ]);
            
            if ($response->successful()) {
                $doctors = $response->json();
                
                if (is_array($doctors)) {
                    foreach ($doctors as $doc) {
                        $match = false;
                        
                        // 1. Verificações robustas do ID do Médico (id_medico ou id principal ou id_pessoa)
                        if (isset($doc['id']) && (int)$doc['id'] === $id) {
                            $match = true;
                        } elseif (isset($doc['id_medico']) && (int)$doc['id_medico'] === $id) {
                            $match = true;
                        } elseif (isset($doc['medico']['id']) && (int)$doc['medico']['id'] === $id) {
                            $match = true;
                        }
                        
                        if ($match) {
                            // 2. Extrai o nome da pessoa vinculada ao médico
                            $nome = $doc['nome'] ?? $doc['pessoa']['nome'] ?? $doc['usuario']['nome'] ?? null;
                            
                            // 3. Extrai o CRM do médico
                            $crm = $doc['crm'] ?? $doc['CRM'] ?? $doc['medico']['crm'] ?? $doc['medico']['CRM'] ?? 'CRM Indisponível';
                            
                            if ($nome) {
                                return [
                                    'id' => $id,
                                    'nome' => $nome,
                                    'crm' => $crm,
                                    'especialidade' => $doc['especialidade'] ?? $doc['medico']['especialidade'] ?? 'Médico'
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Erro ao buscar médicos em /usuarios?funcao=medico na API da Equipe 1: " . $e->getMessage());
        }

        // Fallback para não quebrar a tela em caso de falha da API ou se não encontrar o médico
        return [
            'id' => $id,
            'nome' => "Médico ID #{$id}",
            'crm' => 'CRM Indisponível',
            'especialidade' => 'Indisponível'
        ];
    }
}
