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
}
