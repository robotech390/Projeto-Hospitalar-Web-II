<?php

namespace App\Services;

use App\Repositories\LabExamRepositoryInterface;

class LabResultEntryService
{
    protected $examRepository;

    public function __construct(LabExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getResultEntryData(): array
    {
        $pedidosExames = $this->examRepository->getAllExams();
        $emAnalise = array_filter($pedidosExames, function($o) {
            return $o['status'] === 'Em Análise' || $o['status'] === 'Coletado';
        });
        return [
            'orders' => array_values($emAnalise),
        ];
    }
}
