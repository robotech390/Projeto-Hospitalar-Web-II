<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultationService
{
    protected $patientService;
    protected $doctorService;

    public function __construct(PatientService $patientService, DoctorService $doctorService)
    {
        $this->patientService = $patientService;
        $this->doctorService = $doctorService;
    }

    /**
     * Retorna a URL base da API da Equipe 3.
     */
    protected function getBaseUrl(): string
    {
        return env('API_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Retorna todas as consultas da fila de hoje via API do Grupo 3.
     */
    public function getAllConsultations(): array
    {
        try {
            $token = app(\App\Services\TokenService::class)->getToken();
            $request = Http::timeout(5);
            
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get($this->getBaseUrl() . '/consultas/fila/hoje');
            
            if ($response->successful()) {
                $data = $response->json();
                $consultations = is_array($data) ? $data : [];
                
                // Enriquecer as consultas com dados do paciente e do médico se não vierem aninhados
                foreach ($consultations as &$consultation) {
                    if (is_array($consultation)) {
                        if (!isset($consultation['paciente']) && isset($consultation['id_paciente'])) {
                            $consultation['paciente'] = $this->patientService->getPatientData((int)$consultation['id_paciente']);
                        }
                        if (!isset($consultation['medico']) && isset($consultation['id_medico'])) {
                            $consultation['medico'] = $this->doctorService->getDoctorData((int)$consultation['id_medico']);
                        }
                    }
                }
                unset($consultation);
                
                return $consultations;
            }
        } catch (\Exception $e) {
            Log::error("Erro ao buscar fila de consultas da Equipe 3: " . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Busca dados da consulta, procurando na fila de hoje.
     */
    public function getConsultationData(int $id): ?array
    {
        $consultations = $this->getAllConsultations();
        foreach ($consultations as $consultation) {
            if (isset($consultation['id']) && $consultation['id'] == $id) {
                return $consultation;
            }
        }
        
        return null;
    }
}
