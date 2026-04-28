<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\MedicamentoReceita;
use App\Http\Requests\ReceitaRequest;
use App\Http\Requests\MedicamentoReceitaRequest;
use Illuminate\Http\JsonResponse;

class ReceitaController extends Controller
{
    // GET /api/consultas/{idConsulta}/receitas
    public function index(int $idConsulta): JsonResponse
    {
        $receitas = Receita::with('medicamentos')
            ->where('id_consulta', $idConsulta)
            ->get();

        return response()->json(['success' => true, 'data' => $receitas]);
    }

    // POST /api/consultas/{idConsulta}/receitas
    public function store(ReceitaRequest $request, int $idConsulta): JsonResponse
    {
        $receita = Receita::create(
            array_merge($request->safe()->except('medicamentos'), ['id_consulta' => $idConsulta])
        );

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

        return response()->json([
            'success' => true,
            'message' => 'Receita criada com sucesso.',
            'data'    => $receita->load('medicamentos'),
        ], 201);
    }

    // GET /api/consultas/{idConsulta}/receitas/{id}
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $receita = Receita::with('medicamentos')
            ->where('id_consulta', $idConsulta)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $receita]);
    }

    // PUT /api/consultas/{idConsulta}/receitas/{id}
    public function update(ReceitaRequest $request, int $idConsulta, int $id): JsonResponse
    {
        $receita = Receita::where('id_consulta', $idConsulta)->findOrFail($id);
        $receita->update($request->safe()->except('medicamentos'));

        return response()->json([
            'success' => true,
            'message' => 'Receita atualizada com sucesso.',
            'data'    => $receita->load('medicamentos'),
        ]);
    }

    // DELETE /api/consultas/{idConsulta}/receitas/{id}
    public function destroy(int $idConsulta, int $id): JsonResponse
    {
        $receita = Receita::where('id_consulta', $idConsulta)->findOrFail($id);
        $receita->medicamentos()->delete();
        $receita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receita removida com sucesso.',
        ]);
    }

    // POST /api/consultas/{idConsulta}/receitas/{id}/medicamentos
    public function adicionarMedicamento(MedicamentoReceitaRequest $request, int $idConsulta, int $id): JsonResponse
    {
        $receita = Receita::where('id_consulta', $idConsulta)->findOrFail($id);
        $item = MedicamentoReceita::create(
            array_merge($request->validated(), ['id_receita' => $receita->id])
        );

        return response()->json([
            'success' => true,
            'message' => 'Medicamento adicionado à receita.',
            'data'    => $item,
        ], 201);
    }

    // DELETE /api/consultas/{idConsulta}/receitas/{id}/medicamentos/{idItem}
    public function removerMedicamento(int $idConsulta, int $id, int $idItem): JsonResponse
    {
        $receita = Receita::where('id_consulta', $idConsulta)->findOrFail($id);
        MedicamentoReceita::where('id_receita', $receita->id)->findOrFail($idItem)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medicamento removido da receita.',
        ]);
    }
}
