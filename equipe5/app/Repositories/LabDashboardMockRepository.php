<?php

namespace App\Repositories;

use App\Repositories\LabExamRepositoryInterface;

class LabDashboardMockRepository implements LabDashboardRepositoryInterface
{
    protected $examRepository;

    public function __construct(LabExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }
    public function getWeekData(): array
    {
        $dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $weekData = [];
        foreach ($dias as $dia) {
            $weekData[] = [
                'dia' => $dia,
                'sangue' => mt_rand(5, 24),
                'imagem' => mt_rand(2, 11),
            ];
        }
        return $weekData;
    }

    public function getRevenueData(): array
    {
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $revenueData = [];
        foreach ($meses as $mes) {
            $revenueData[] = [
                'mes' => $mes,
                'valor' => mt_rand(20, 50) / 100.0 * 132,
            ];
        }
        return $revenueData;
    }

    public function getRevenueToday(): float
    {
        return mt_rand(20, 50) / 10.0;
    }
}
