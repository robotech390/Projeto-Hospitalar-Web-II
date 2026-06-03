<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Receita;
use App\Models\MedicamentoReceita;
use App\Http\Requests\ReceitaRequest;
use Illuminate\Http\JsonResponse;

class ReceitaController extends Controller
{
    // View: lista de receitas (documentação OpenAPI mantida nas rotas API)
    public function lista()
    {
        $receitas = Receita::with('consulta')->get();
        return view('prontuario.receitas', compact('receitas'));
    }
    // View: formulário de receita
    public function formulario(?int $consultaId = null)
    {
        $consultas = Consulta::all();
        $medicamentos = Medicamento::all();
        $selectedConsulta = $consultaId;

        return view('prontuario.receitaForm', compact('consultas', 'medicamentos', 'selectedConsulta'));
    }
    // Ações de criação, leitura, atualização e remoção
    public function salvar(ReceitaRequest $request)
    {
        $receita = Receita::create($request->safe()->except('medicamentos'));

        if ($request->filled('medicamentos')) {
            foreach ($request->medicamentos as $med) {
                MedicamentoReceita::create([
                    'id_receita'     => $receita->id,
                    'id_medicamento' => $med['id_medicamento'],
                    'posologia'      => $med['posologia'] ?? null,
                    'quantidade'     => $med['quantidade'] ?? 1,
                ]);
            }
        }

        return redirect()->route('receitas.index')->with('success', 'Receita criada com sucesso.');
    }
    // View: formulário de edição de receita
    public function editar(int $id)
    {
        $receita = Receita::with('medicamentos')->findOrFail($id);
        $consultas = Consulta::all();
        $medicamentos = Medicamento::all();

        return view('prontuario.receitaForm', compact('receita', 'consultas', 'medicamentos'));
    }
    // Ação de atualização de receita
    public function atualizar(ReceitaRequest $request, int $id)
    {
        $receita = Receita::findOrFail($id);
        $receita->update($request->safe()->except('medicamentos'));

        $receita->medicamentos()->delete();
        if ($request->filled('medicamentos')) {
            foreach ($request->medicamentos as $med) {
                MedicamentoReceita::create([
                    'id_receita'     => $receita->id,
                    'id_medicamento' => $med['id_medicamento'],
                    'posologia'      => $med['posologia'] ?? null,
                    'quantidade'     => $med['quantidade'] ?? 1,
                ]);
            }
        }

        return redirect()->route('receitas.index')->with('success', 'Receita atualizada com sucesso.');
    }
    // Ação de remoção de receita
    public function remover(int $id)
    {
        $receita = Receita::findOrFail($id);
        $receita->medicamentos()->delete();
        $receita->delete();

        return redirect()->route('receitas.index')->with('success', 'Receita removida com sucesso.');
    }

    public function show(int $id): JsonResponse
    {
        $receita = Receita::with('medicamentos')->findOrFail($id);

        return response()->json(['success' => true, 'data' => $receita]);
    }

    /**
     * @OA\Get(
     *     path="/api/receitas",
     *     tags={"Receita"},
     *     summary="Obter lista de receitas",
     *     description="Retorna uma lista de todas as receitas do sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de receitas retornada com sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Receita"))
     *     )
     * )
     */
    public function all(): JsonResponse
    {
        $receitas = Receita::with('medicamentos', 'consulta')->get();

        return response()->json([
            'success' => true,
            'data'    => $receitas,
        ]);
    }
}
