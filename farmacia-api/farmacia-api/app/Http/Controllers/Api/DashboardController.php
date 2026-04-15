<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicamento;
use App\Models\Lote;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProdutos = Medicamento::count();
        
        // Busca lotes com menos de 50 unidades ou vencendo em 30 dias
        $alertas = Lote::with('medicamento')
            ->where('quantidade_produtos', '<=', 50)
            ->orWhere('data_validade', '<=', now()->addDays(30))
            ->get()
            ->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'codigo' => $lote->id_produto, 
                    'medicamento' => $lote->medicamento ? $lote->medicamento->nome : 'Desconhecido',
                    'lote' => $lote->numero,
                    'quantidade' => $lote->quantidade_produtos,
                    'status' => $lote->quantidade_produtos <= 50 ? 'Estoque Crítico' : 'Vence em breve'
                ];
            });

        return response()->json([
            'kpis' => [
                'total_produtos' => $totalProdutos,
                'alertas_quantidade' => $alertas->count(),
                'dispensacoes_hoje' => 0, // Como você não tem tabela de dispensação, não há como calcular isso no banco. Fica 0.
            ],
            'alertas_detalhados' => $alertas
        ]);
    }
}