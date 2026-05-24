<?php

namespace App\Services;

use App\Repositories\LabSolicitationRepositoryInterface;
use App\Models\SolicitacaoExame;

class LabSolicitationService
{
    protected $repository;
    protected $consultationService;
    protected $catalogService;

    public function __construct(
        LabSolicitationRepositoryInterface $repository,
        ConsultationService $consultationService,
        LabCatalogService $catalogService
    ) {
        $this->repository = $repository;
        $this->consultationService = $consultationService;
        $this->catalogService = $catalogService;
    }

    public function getSolicitationsData(): array
    {
        // Pega todas as solicitações da base do Lab (Equipe 5)
        $solicitations = $this->repository->getAll();
        
        // Pega as consultas da fila de hoje da API (Equipe 3)
        $consultations = $this->consultationService->getAllConsultations();
        
        $formatted = [];

        foreach ($solicitations as $sol) {
            // Busca os dados da consulta específica na lista carregada da API
            $consultation = null;
            foreach ($consultations as $c) {
                if (isset($c['id']) && $c['id'] == $sol['id_consulta']) {
                    $consultation = $c;
                    break;
                }
            }
            
            // Extrai paciente e médico da API (com fallbacks se ausentes)
            $paciente = $consultation['paciente']['nome'] ?? $consultation['paciente'] ?? 'Desconhecido';
            $medico = $consultation['medico']['nome'] ?? $consultation['medico'] ?? 'Desconhecido';

            $formatted[] = array_merge($sol, [
                'id' => $sol['id'],
                'consulta_data' => $consultation,
                'paciente' => $paciente,
                'medico' => $medico,
            ]);
        }

        return [
            'solicitations' => $formatted,
            'consultations' => $consultations,
            'examTypes' => $this->catalogService->getCatalogData()['catalogoExames'] ?? [],
        ];
    }

    public function createSolicitation(array $data): SolicitacaoExame
    {
        return $this->repository->create($data);
    }

    public function updateSolicitation(int $id, array $data): SolicitacaoExame
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSolicitation(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
