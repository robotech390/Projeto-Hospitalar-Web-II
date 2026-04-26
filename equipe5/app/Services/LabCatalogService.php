<?php

namespace App\Services;

use App\Repositories\LabCatalogRepositoryInterface;

class LabCatalogService
{
    protected $catalogRepository;

    public function __construct(LabCatalogRepositoryInterface $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function getCatalogData(): array
    {
        return [
            'catalogoExames' => $this->catalogRepository->getAllCatalog(),
        ];
    }

    public function createExam(array $data): \App\Models\TipoExame
    {
        return $this->catalogRepository->store($data);
    }

    public function updateExam(string $id, array $data): \App\Models\TipoExame
    {
        return $this->catalogRepository->update($id, $data);
    }

    public function deleteExam(string $id): bool
    {
        return $this->catalogRepository->delete($id);
    }
}
