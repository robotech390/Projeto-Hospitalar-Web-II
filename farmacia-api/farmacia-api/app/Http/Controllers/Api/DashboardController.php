<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // DADOS FICTÍCIOS (MOCK) - Substituir por chamadas ao BD depois
        
        $totalProdutos = 1240;
        $dispensacoesHoje = 342;
        
        $alertasEstoque = [
            [
                'id' => 1,
                'codigo' => 'PRD-001',
                'medicamento' => 'Dipirona Sódica 500mg',
                'lote' => 'LT-8842',
                'quantidade' => 50,
                'status' => 'Estoque Crítico'
            ],
            [
                'id' => 2,
                'codigo' => 'PRD-045',
                'medicamento' => 'Amoxicilina 875mg',
                'lote' => 'LT-9910',
                'quantidade' => 120,
                'status' => 'Vence em 15 dias'
            ]
        ];

        return response()->json([
            'kpis' => [
                'total_produtos' => $totalProdutos,
                'alertas_quantidade' => count($alertasEstoque),
                'dispensacoes_hoje' => $dispensacoesHoje,
            ],
            'alertas_detalhados' => $alertasEstoque
        ]);
    }
}