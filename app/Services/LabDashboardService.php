<?php

namespace App\Services;

use App\Repositories\LabExamRepositoryInterface;

class LabDashboardService
{
    protected $examRepository;

    public function __construct(LabExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getDashboardData(): array
    {
        $pedidosExames = $this->examRepository->getAllExams();
        $pendentes = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Pendente'));
        $emAnalise = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Em Análise'));
        $concluidosHoje = count(array_filter($pedidosExames, fn($e) => $e['status'] === 'Concluído'));
        $receitaHoje = $concluidosHoje * (mt_rand(20, 50) / 100.0);

        $dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $weekData = [];
        foreach ($dias as $dia) {
            $weekData[] = [
                'dia' => $dia,
                'sangue' => mt_rand(5, 24),
                'imagem' => mt_rand(2, 11),
            ];
        }
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $revenueData = [];
        foreach ($meses as $mes) {
            $revenueData[] = [
                'mes' => $mes,
                'valor' => mt_rand(20, 50) / 100.0 * 132,
            ];
        }
        $upcomingExams = array_filter($pedidosExames, function($e) {
            return in_array($e['status'], ['Pendente', 'Coletado']);
        });
        usort($upcomingExams, function($a, $b) {
            return strcmp($a['horario'], $b['horario']);
        });
        $upcomingExams = array_slice(array_values($upcomingExams), 0, 5);

        return [
            // 'pendentes' => [
            //     'value' => $pendentes,
            //     'trendValue' => 1,
            //     'trendPercentual' => false,
            // ],
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
                'trendValue' => 10,
                'trendPercentual' => true,
            ],
            'weekData' => $weekData,
            'revenueData' => $revenueData,
            'upcomingExams' => $upcomingExams,
        ];
    }
}
