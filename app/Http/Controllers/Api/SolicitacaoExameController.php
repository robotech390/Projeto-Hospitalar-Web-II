<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\SolicitacaoExame;
use App\Models\ItensExame;
use App\Http\Requests\SolicitacaoExameRequest;
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
     *     path="/api/solicitacoes-exame/{id}",
     *     tags={"Solicitação de Exame"},
     *     summary="Obter detalhes de uma solicitação de exame",
     *     description="Retorna os detalhes de uma solicitação de exame específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da solicitação de exame",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação de exame encontrada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoExame")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::with(['consulta', 'itens'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $solicitacao,
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