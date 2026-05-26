<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispensacaoController extends Controller
{
    public function show($id)
    {
        $itemReceita = DB::table('medicamento_receita')
            ->join('medicamento', 'medicamento_receita.id_medicamento', '=', 'medicamento.id')
            ->where('medicamento_receita.id', $id)
            ->select(
                'medicamento_receita.id as id_item',
                'medicamento_receita.quantidade as qtd_receitada',
                // Removida a busca pelo 'status' que estava quebrando o código
                'medicamento.nome as nome_medicamento',
                'medicamento.id as id_medicamento'
            )->first();

        if (!$itemReceita) {
            return response()->json(['erro' => 'Item não encontrado na tabela medicamento_receita.'], 404);
        }

        $lote = Lote::where('id_medicamento', $itemReceita->id_medicamento)
            ->where('ativo', 1)
            ->where('quantidade_produtos', '>=', $itemReceita->qtd_receitada)
            ->orderBy('data_validade', 'asc')
            ->first();

        if (!$lote) {
            return response()->json(['erro' => 'Não há lotes com estoque suficiente para a quantidade solicitada.'], 400);
        }

        return response()->json([
            'id_item_receita' => $itemReceita->id_item,
            'medicamento' => $itemReceita->nome_medicamento,
            'qtd_receitada' => $itemReceita->qtd_receitada,
            'lote' => [
                'id' => $lote->id,
                'numero' => $lote->numero,
                'estoque_atual' => $lote->quantidade_produtos
            ]
        ]);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $lote = Lote::find($request->id_lote);

            if (!$lote || $lote->ativo == 0) {
                return response()->json(['erro' => 'Lote inválido ou inativo'], 404);
            }

            if ($lote->quantidade_produtos < $request->quantidade) {
                return response()->json(['erro' => 'Estoque físico insuficiente no lote'], 400);
            }

            $lote->quantidade_produtos -= $request->quantidade;
            if ($lote->quantidade_produtos <= 0) {
                $lote->ativo = 0;
            }
            $lote->save();

            return response()->json(['mensagem' => 'Dispensação confirmada e estoque atualizado!']);
        });
    }
}