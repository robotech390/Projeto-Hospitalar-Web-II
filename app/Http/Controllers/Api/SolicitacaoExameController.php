<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitacaoExame;
use App\Models\ItensExame;
use App\Http\Requests\SolicitacaoExameRequest;
use App\Http\Requests\ItensExameRequest;
use Illuminate\Http\JsonResponse;

class SolicitacaoExameController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame",
     *     tags={"Solicitação de Exame"},
     *     summary="Listar todas as solicitações de exame para uma consulta",
     *     description="Retorna uma lista de todas as solicitações de exame associadas a uma consulta específica.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitações de exame",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     )
     * )
     */
    public function index(int $idConsulta): JsonResponse
    {
        $solicitacoes = SolicitacaoExame::with('itens')
            ->where('id_consulta', $idConsulta)
            ->get();

        return response()->json(['success' => true, 'data' => $solicitacoes]);
    }

    /**
     * @OA\POST(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame",
     *     tags={"Solicitação de Exame"},
     *     summary="Criar uma nova solicitação de exame",
     *     description="Cria uma nova solicitação de exame para uma consulta específica.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitação de exame criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     )
     * )
     */
    public function store(SolicitacaoExameRequest $request, int $idConsulta): JsonResponse
    {
        $solicitacao = SolicitacaoExame::create([
            'data'          => now(),
            'justificativa' => $request->justificativa,
            'prioridade'    => $request->prioridade,
            'id_consulta'   => $idConsulta,
        ]);

        if ($request->filled('itens')) {
            foreach ($request->itens as $item) {
                ItensExame::create([
                    'id_solicitacao' => $solicitacao->id,
                    'id_tipo_exame'  => $item['id_tipo_exame'],
                    'status'         => 'pendente',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de exame criada com sucesso.',
            'data'    => $solicitacao->load('itens'),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame/{id}",
     *     tags={"Solicitação de Exame"},
     *     summary="Obter detalhes de uma solicitação de exame",
     *     description="Retorna os detalhes de uma solicitação de exame específica com base no ID fornecido.",
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
     *         description="ID da solicitação de exame",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da solicitação de exame",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitação de exame não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::with('itens')
            ->where('id_consulta', $idConsulta)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $solicitacao]);
    }

    /**
     * @OA\Put(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame/{id}",
     *     tags={"Solicitação de Exame"},
     *     summary="Atualizar uma solicitação de exame existente",
     *     description="Atualiza os detalhes de uma solicitação de exame específica com base nos IDs fornecidos.",
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
     *         description="ID da solicitação de exame",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação de exame atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitação de exame não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function update(SolicitacaoExameRequest $request, int $idConsulta, int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::where('id_consulta', $idConsulta)->findOrFail($id);
        $solicitacao->update($request->safe()->except('itens'));

        return response()->json([
            'success' => true,
            'message' => 'Solicitação atualizada com sucesso.',
            'data'    => $solicitacao->load('itens'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame/{id}",
     *     tags={"Solicitação de Exame"},
     *     summary="Remover uma solicitação de exame",
     *     description="Remove uma solicitação de exame específica com base nos IDs fornecidos.",
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
     *         description="ID da solicitação de exame",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação de exame removida com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitação de exame não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *    )
     * )
    */
    public function destroy(int $idConsulta, int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::where('id_consulta', $idConsulta)->findOrFail($id);
        $solicitacao->itens()->delete();
        $solicitacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Solicitação removida com sucesso.',
        ]);
    }

    /**
     * @OA\POST(
     *    path="/api/consultas/{idConsulta}/solicitacoes-exame/{id}/itens",
     *    tags={"Solicitação de Exame"},
     *    summary="Adicionar um item à solicitação de exame",
     *    description="Adiciona um novo item à solicitação de exame específica com base nos IDs fornecidos.",
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
     *        description="ID da solicitação de exame",
     *        required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(ref="#/components/schemas/ItensExame")
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Item adicionado à solicitação de exame com sucesso",
     *        @OA\JsonContent(ref="#/components/schemas/ItensExame")
     *    )
     * )
     */
    public function adicionarItem(ItensExameRequest $request, int $idConsulta, int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::where('id_consulta', $idConsulta)->findOrFail($id);
        $item = ItensExame::create([
            'id_solicitacao' => $solicitacao->id,
            'id_tipo_exame'  => $request->id_tipo_exame,
            'status'         => 'pendente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Exame adicionado à solicitação.',
            'data'    => $item,
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/consultas/{idConsulta}/solicitacoes-exame/{id}/itens/{idItem}",
     *     tags={"Solicitação de Exame"},
     *     summary="Remover um item da solicitação de exame",
     *     description="Remove um item específico de uma solicitação de exame com base nos IDs fornecidos.",
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
     *         description="ID da solicitação de exame",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idItem",
     *         in="path",
     *         description="ID do item a ser removido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removido da solicitação de exame com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitação de exame ou item não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function removerItem(int $idConsulta, int $id, int $idItem): JsonResponse
    {
        $solicitacao = SolicitacaoExame::where('id_consulta', $idConsulta)->findOrFail($id);
        ItensExame::where('id_solicitacao', $solicitacao->id)->findOrFail($idItem)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exame removido da solicitação.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/pacientes/{idPaciente}/resultados-exame",
     *     tags={"Solicitação de Exame"},
     *     summary="Obter resultados de exames para um paciente",
     *     description="Retorna os resultados de exames concluídos para um paciente específico.",
     *     @OA\Parameter(
     *         name="idPaciente",
     *         in="path",
     *         description="ID do paciente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados de exames obtidos com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function resultadosPaciente(int $idPaciente): JsonResponse
    {
        $itens = ItensExame::with(['solicitacao.consulta'])
            ->whereHas('solicitacao.consulta', fn($q) => $q->where('id_paciente', $idPaciente))
            ->where('status', 'concluido')
            ->orderByDesc('data_resultado')
            ->get();

        return response()->json(['success' => true, 'data' => $itens]);
    }
}
