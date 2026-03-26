<?php

namespace App\Services;

use App\Repositories\LabExamRepositoryInterface;

class LabCollectionQueueService
{
    protected $examRepository;

    public function __construct(LabExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getQueueData(): array
    {
        $pedidosExames = $this->examRepository->getAllExams();
        $fila = array_filter($pedidosExames, function($o) {
            return $o['status'] === 'Pendente' || $o['status'] === 'Coletado';
        });
        return [
            'orders' => array_values($fila),
        ];
    }
}
