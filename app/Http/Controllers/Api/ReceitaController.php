<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Receita;
use App\Models\MedicamentoReceita;
use App\Http\Requests\ReceitaRequest;
use App\Http\Requests\MedicamentoReceitaRequest;
use Illuminate\Http\JsonResponse;

class ReceitaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/receitas",
     *     tags={"Receita"},
     *     summary="Obter lista de receitas",
     *     description="Retorna uma lista de todas as receitas.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de receitas retornado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     )
     * )
     */
    public function lista()
    {
        $receitas = Receita::with('consulta')->get();
        return view('prontuario.receitas', compact('receitas'));
    }

    public function formulario(?int $consultaId = null)
    {
        $consultas = Consulta::all();
        $medicamentos = Medicamento::all();
        $selectedConsulta = $consultaId;

        return view('prontuario.receitaForm', compact('consultas', 'medicamentos', 'selectedConsulta'));
    }

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

    public function mostrar(int $id)
    {
        return redirect()->route('receitas.index');
    }

    public function editar(int $id)
    {
        $receita = Receita::with('medicamentos')->findOrFail($id);
        $consultas = Consulta::all();
        $medicamentos = Medicamento::all();

        return view('prontuario.receitaForm', compact('receita', 'consultas', 'medicamentos'));
    }

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

    public function remover(int $id)
    {
        $receita = Receita::findOrFail($id);
        $receita->medicamentos()->delete();
        $receita->delete();

        return redirect()->route('receitas.index')->with('success', 'Receita removida com sucesso.');
    }

    /**
     * @OA\Get(
     *     path="/api/consultas/{idConsulta}/receitas",
     *     tags={"Receita"},
     *     summary="Obter todas as receitas de uma consulta",
     *     description="Retorna todas as receitas de uma consulta específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receitas encontradas com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     )
     * )
     */
    public function index(int $idConsulta): JsonResponse
    {
        $receitas = Receita::with('medicamentos')
            ->where('id_consulta', $idConsulta)
            ->get();

        return response()->json(['success' => true, 'data' => $receitas]);
    }

    /**
     * @OA\POST(
     *     path="/api/consultas/{idConsulta}/receitas",
     *     tags={"Receita"},
     *     summary="Criar uma nova receita",
     *     description="Cria uma nova receita para uma consulta específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Receita criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/consultas/{idConsulta}/receitas/{id}",
     *     tags={"Receita"},
     *     summary="Obter detalhes de uma receita",
     *     description="Retorna os detalhes de uma receita específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da receita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receita encontrada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     )
     * )
     */
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $receita = Receita::with('medicamentos')
            ->where('id_consulta', $idConsulta)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $receita]);
    }

    /**
     * @OA\Put(
     *     path="/api/consultas/{idConsulta}/receitas/{id}",
     *     tags={"Receita"},
     *     summary="Atualizar uma receita existente",
     *     description="Atualiza uma receita específica para uma consulta com base nos IDs fornecidos.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da receita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receita atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/consultas/{idConsulta}/receitas/{id}",
     *     tags={"Receita"},
     *     summary="Remover uma receita existente",
     *     description="Remove uma receita específica para uma consulta com base nos IDs fornecidos.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da receita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Receita removida com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Receita")
     *     )
     * )
     */
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

    /**
     * @OA\POST(
     *    path="/api/consultas/{idConsulta}/receitas/{id}/medicamentos",
     *    tags={"Receita"},
     *    summary="Adicionar um medicamento à receita",
     *    description="Adiciona um medicamento a uma receita específica para uma consulta com base nos IDs fornecidos.",
     *    @OA\Parameter(
     *        name="idConsulta",
     *        in="path",
     *        description="ID da consulta",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="ID da receita",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(ref="#/components/schemas/MedicamentoReceita")
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Medicamento adicionado à receita com sucesso",
     *        @OA\JsonContent(ref="#/components/schemas/MedicamentoReceita")
     *    )
     * )
     */
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

    /**
     * @OA\Delete(
     *    path="/api/consultas/{idConsulta}/receitas/{id}/medicamentos/{idItem}",
     *    tags={"Receita"},
     *    summary="Remover um medicamento da receita",
     *    description="Remove um medicamento de uma receita específica para uma consulta com base nos IDs fornecidos.",
     *    @OA\Parameter(
     *        name="idConsulta",
     *        in="path",
     *        description="ID da consulta",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="ID da receita",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(
     *        name="idItem",
     *        in="path",
     *        description="ID do medicamento na receita",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Medicamento removido da receita com sucesso",
     *        @OA\JsonContent(ref="#/components/schemas/MedicamentoReceita")
     *    )
     * )
     */
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
