<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\Request;

class DispensacaoController extends Controller
{
    public function index()
    {
        // Só retorna lotes que estejam ativos E que tenham estoque real
        return response()->json(
            Lote::with('medicamento')
                ->where('ativo', 1) 
                ->where('quantidade_produtos', '>', 0)
                ->get()
        );
    }

    public function store(Request $request)
    {
        try {
            $lote = Lote::find($request->id_lote);

            if (!$lote || $lote->ativo == 0) {
                return response()->json(['erro' => 'Lote não encontrado ou já desativado'], 404);
            }

            if ($lote->quantidade_produtos < $request->quantidade) {
                return response()->json(['erro' => 'Estoque insuficiente no lote'], 400);
            }

            // Realiza a subtração
            $lote->quantidade_produtos -= $request->quantidade;

            // REGRA: Se zerar o estoque, passa o ativo para 0
            if ($lote->quantidade_produtos <= 0) {
                $lote->ativo = 0;
            }

            $lote->save();

            return response()->json(['mensagem' => 'Dispensação realizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }
}