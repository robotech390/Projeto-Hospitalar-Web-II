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
    // GET /api/consultas/{idConsulta}/solicitacoes-exame
    public function index(int $idConsulta): JsonResponse
    {
        $solicitacoes = SolicitacaoExame::with('itens')
            ->where('id_consulta', $idConsulta)
            ->get();

        return response()->json(['success' => true, 'data' => $solicitacoes]);
    }

    // POST /api/consultas/{idConsulta}/solicitacoes-exame
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

    // GET /api/consultas/{idConsulta}/solicitacoes-exame/{id}
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $solicitacao = SolicitacaoExame::with('itens')
            ->where('id_consulta', $idConsulta)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $solicitacao]);
    }

    // PUT /api/consultas/{idConsulta}/solicitacoes-exame/{id}
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

    // DELETE /api/consultas/{idConsulta}/solicitacoes-exame/{id}
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

    // POST /api/consultas/{idConsulta}/solicitacoes-exame/{id}/itens
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

    // DELETE /api/consultas/{idConsulta}/solicitacoes-exame/{id}/itens/{idItem}
    public function removerItem(int $idConsulta, int $id, int $idItem): JsonResponse
    {
        $solicitacao = SolicitacaoExame::where('id_consulta', $idConsulta)->findOrFail($id);
        ItensExame::where('id_solicitacao', $solicitacao->id)->findOrFail($idItem)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exame removido da solicitação.',
        ]);
    }

    // GET /api/pacientes/{idPaciente}/resultados-exame
    // Leitura dos resultados vindos do Grupo 5
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
