<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoMedicamento;
use Illuminate\Http\Request;

class TipoMedicamentoController extends Controller
{
    public function index()
    {
        return response()->json(TipoMedicamento::all());
    }

    public function store(Request $request)
    {
        try {
            // Busca um tipo com a mesma descrição (ignorando maiúsculas/minúsculas)
            // Se encontrar, retorna o existente. Se não, cria um novo.
            $tipo = TipoMedicamento::firstOrCreate(
                ['descricao' => trim($request->descricao)]
            );
            
            return response()->json($tipo, 201);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }
}