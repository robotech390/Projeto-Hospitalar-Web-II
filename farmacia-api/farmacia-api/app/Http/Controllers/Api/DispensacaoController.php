<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\Request;

class DispensacaoController extends Controller
{
    public function index()
    {
        // Envia para o React apenas lotes que possuem estoque positivo
        return response()->json(
            Lote::with('medicamento')->where('quantidade_produtos', '>', 0)->get()
        );
    }

    public function store(Request $request)
    {
        $lote = Lote::find($request->id_lote);
        
        if (!$lote) {
            return response()->json(['erro' => 'Lote não encontrado no banco de dados'], 404);
        }
        
        if ($lote->quantidade_produtos < $request->quantidade) {
            return response()->json(['erro' => 'Estoque insuficiente no lote selecionado'], 400);
        }

        // Subtração direta destrutiva (Risco arquitetural mantido)
        $lote->quantidade_produtos -= $request->quantidade;
        $lote->save();

        return response()->json(['mensagem' => 'Estoque subtraído com sucesso.']);
    }
}