<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\SolicitacaoExame;
use App\Models\ItensExame;
use App\Http\Requests\SolicitacaoExameRequest;
use App\Http\Requests\ItensExameRequest;
use Illuminate\Http\JsonResponse;

class SolicitacaoExameController extends Controller
{
    // ========== MÉTODOS WEB ==========

    // View: lista de solicitações (documentação OpenAPI mantida nas rotas API)
    public function lista()
    {
        $solicitacoes = SolicitacaoExame::with('consulta', 'itens')->get();
        return view('prontuario.solicitacoesExame', compact('solicitacoes'));
    }

    // View: formulário de solicitação (documentação OpenAPI mantida nas rotas API)
    public function formulario(?int $consultaId = null)
    {
        $consultas = Consulta::all();
        $selectedConsulta = $consultaId;
        return view('prontuario.solicitacaoExameForm', compact('consultas', 'selectedConsulta'));
    }

    // Web: salvar solicitação (documentação OpenAPI mantida nas rotas API)
    public function salvar(SolicitacaoExameRequest $request)
    {
        $solicitacao = SolicitacaoExame::create([
            'data'          => now(),
            'justificativa' => $request->justificativa,
            'prioridade'    => $request->prioridade,
            'id_consulta'   => $request->id_consulta,
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

        return redirect()->route('solicitacoesExame.index')->with('success', 'Solicitação de exame criada com sucesso.');
    }

    // View: detalhes da solicitação (documentação OpenAPI mantida nas rotas API)
    public function mostrar(int $id)
    {
        $solicitacao = SolicitacaoExame::with('consulta', 'itens')->findOrFail($id);
        return view('prontuario.solicitacaoExameDetalhes', compact('solicitacao'));
    }

    // View: editar solicitação (documentação OpenAPI mantida nas rotas API)
    public function editar(int $id)
    {
        $solicitacao = SolicitacaoExame::with('itens')->findOrFail($id);
        $consultas = Consulta::all();
        return view('prontuario.solicitacaoExameForm', compact('solicitacao', 'consultas'));
    }

    // Web: atualizar solicitação (documentação OpenAPI mantida nas rotas API)
    public function atualizar(SolicitacaoExameRequest $request, int $id)
    {
        $solicitacao = SolicitacaoExame::findOrFail($id);
        $solicitacao->update([
            'justificativa' => $request->justificativa,
            'prioridade'    => $request->prioridade,
            'id_consulta'   => $request->id_consulta,
        ]);

        $solicitacao->itens()->delete();
        if ($request->filled('itens')) {
            foreach ($request->itens as $item) {
                ItensExame::create([
                    'id_solicitacao' => $solicitacao->id,
                    'id_tipo_exame'  => $item['id_tipo_exame'],
                    'status'         => 'pendente',
                ]);
            }
        }

        return redirect()->route('solicitacoesExame.index')->with('success', 'Solicitação de exame atualizada com sucesso.');
    }

    // Web: remover solicitação (documentação OpenAPI mantida nas rotas API)
    public function remover(int $id)
    {
        $solicitacao = SolicitacaoExame::findOrFail($id);
        $solicitacao->itens()->delete();
        $solicitacao->delete();
        return redirect()->route('solicitacoesExame.index')->with('success', 'Solicitação de exame deletada com sucesso.');
    }

    // ========== MÉTODOS API ==========

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
     *         description="Item removido da solicitação com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/ItensExame")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item não encontrado",
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
            'message' => 'Item removido da solicitação.',
        ]);
    }

    /**
     * Lê resultados de exames de um paciente (leitura do Grupo 5)
     */
    public function resultadosPaciente(int $idPaciente): JsonResponse
    {
        $itens = ItensExame::with(['solicitacao'])
            ->whereHas('solicitacao', function ($q) use ($idPaciente) {
                $q->whereHas('consulta', function ($qq) use ($idPaciente) {
                    $qq->where('id_paciente', $idPaciente);
                });
            })
            ->whereNotNull('data_resultado')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $itens,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/solicitacoes-exame",
     *     tags={"Solicitação de Exame"},
     *     summary="Listar todas as solicitações de exame",
     *     description="Retorna todas as solicitações de exame do sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitações retornada com sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SolicitacaoExame"))
     *     )
     * )
     */
    public function all(): JsonResponse
    {
        $solicitacoes = SolicitacaoExame::with('consulta', 'itens')->get();

        return response()->json([
            'success' => true,
            'data'    => $solicitacoes,
        ]);
    }
}