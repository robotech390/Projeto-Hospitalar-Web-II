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
        $solicitations = $this->repository->getAll();
        $formatted = [];

        foreach ($solicitations as $sol) {
            $consultation = $this->consultationService->getConsultationData($sol['id_consulta']);
            
            $formatted[] = array_merge($sol, [
                'id' => $sol['id'],
                'consulta_data' => $consultation,
                'paciente' => $consultation['paciente']['nome'] ?? 'Desconhecido',
                'medico' => $consultation['medico']['nome'] ?? 'Desconhecido',
            ]);
        }

        return [
            'solicitations' => $formatted,
            'consultations' => $this->consultationService->getAllConsultations(),
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
