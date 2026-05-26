<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index()
    {
        // Retorna todos os medicamentos que já existem no MySQL
        return response()->json(Medicamento::all());
    }

    public function store(Request $request)
    {
        try {
            $medicamento = Medicamento::create($request->all());
            return response()->json($medicamento, 201);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $medicamento = Medicamento::find($id);
            if (!$medicamento) return response()->json(['erro' => 'Não encontrado'], 404);

            $dados = $request->except(['principio_ativo']);
            
            $medicamento->update($dados);
            return response()->json($medicamento);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }
}