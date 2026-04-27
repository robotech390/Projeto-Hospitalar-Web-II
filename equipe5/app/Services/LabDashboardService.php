<?php

namespace App\Services;

use App\Repositories\LabExamRepositoryInterface;
use App\Repositories\LabDashboardRepositoryInterface;

class LabDashboardService
{
    protected $examRepository;
    protected $dashboardRepository;

    public function __construct(
        LabExamRepositoryInterface $examRepository,
        LabDashboardRepositoryInterface $dashboardRepository
    ) {
        $this->examRepository = $examRepository;
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(): array
    {
        $pedidosExames = $this->examRepository->getAllExams();
        $pendentes = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Pendente'));
        $emAnalise = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Em Análise'));
        $concluidosHoje = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Concluído'));
        $receitaHoje = $this->dashboardRepository->getRevenueToday();

        $upcomingExams = array_filter($pedidosExames, function($e) {
            return in_array($e['status'], ['Pendente', 'Coletado']);
        });
        usort($upcomingExams, function($a, $b) {
            return strcmp($a['horario'], $b['horario']);
        });
        $upcomingExams = array_slice(array_values($upcomingExams), 0, 5);

        $weekData = $this->dashboardRepository->getWeekData();
        $revenueData = $this->dashboardRepository->getRevenueData();

        return [
            'pendentes' => [
                'value' => $pendentes,
                'trendValue' => 0,
                'trendPercentual' => false,
            ],
            'emAnalise' => [
                'value' => $emAnalise,
                'trendValue' => 0,
                'trendPercentual' => false,
            ],
            'concluidosHoje' => [
                'value' => $concluidosHoje,
                'trendValue' => 0,
                'trendPercentual' => true,
            ],
            'receitaHoje' => [
                'value' => $receitaHoje,
                'trendValue' => 0,
                'trendPercentual' => true,
            ],
            'weekData' => $weekData,
            'revenueData' => $revenueData,
            'upcomingExams' => $upcomingExams,
        ];
    }
}
