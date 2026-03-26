<?php

namespace App\Services;

use App\Repositories\LabExamRepositoryInterface;

class LabExamStatusService
{
    protected $examRepository;

    public function __construct(LabExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getExamStatusData(): array
    {
        $pedidosExames = $this->examRepository->getAllExams();
        $counts = [];
        foreach ($pedidosExames as $o) {
            $counts[$o['status']] = ($counts[$o['status']] ?? 0) + 1;
        }
        return [
            'orders' => $pedidosExames,
            'counts' => $counts,
        ];
    }
}
