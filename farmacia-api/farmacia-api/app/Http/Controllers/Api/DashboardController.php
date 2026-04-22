<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicamento;
use App\Models\Lote;

class DashboardController extends Controller
{
    public function index()
    {
        // Conta quantos medicamentos existem no catálogo
        $totalProdutos = Medicamento::count();
        
        // Lógica de Alertas que você perguntou:
        // Busca lotes ativos que estão com estoque baixo (<= 50) ou vencendo em 30 dias
        $alertas = Lote::with('medicamento')
            ->where('ativo', 1) 
            ->where(function($query) {
                $query->where('quantidade_produtos', '<=', 50)
                      ->orWhere('data_validade', '<=', now()->addDays(30));
            })
            ->get()
            ->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'codigo' => $lote->id_medicamento, // FK ajustada para o seu SQL
                    'medicamento' => $lote->medicamento ? $lote->medicamento->nome : 'Desconhecido',
                    'lote' => $lote->numero,
                    'quantidade' => $lote->quantidade_produtos,
                    'status' => $lote->quantidade_produtos <= 50 ? 'Estoque Crítico' : 'Vence em breve'
                ];
            });

        // Retorna o JSON formatado para o React ler no Dashboard.tsx
        return response()->json([
            'kpis' => [
                'total_produtos' => $totalProdutos,
                'alertas_quantidade' => $alertas->count(),
                'dispensacoes_hoje' => 0, // Como você não tem tabela de histórico, fica 0 por enquanto
            ],
            'alertas_detalhados' => $alertas
        ]);
    }
}