<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendaRequest;
use App\Models\Agenda;
use App\Models\Medico;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AgendaController extends Controller
{
    /**
     * Formata o médico para retorno (sem buscar 'nome' direto na tabela medico).
     */
    private function formatarMedico(Medico $m): array
    {
        return [
            'id'            => $m->id,
            'nome'          => $m->pessoa?->nome,
            'crm'           => $m->crm,
            'uf_crm'        => $m->uf_crm,
            'especialidade' => $m->especialidade,
        ];
    }

    /**
     * Formata o plantão para retorno. Garante que `data_disponibilidade` venha
     * como string YYYY-MM-DD (sem timezone), evitando bugs de formatação no frontend.
     */
    private function formatarAgenda(Agenda $a): array
    {
        return [
            'id'                   => $a->id,
            'id_medico'            => $a->id_medico,
            'data_disponibilidade' => $a->data_disponibilidade
                ? (is_string($a->data_disponibilidade) ? $a->data_disponibilidade : $a->data_disponibilidade->format('Y-m-d'))
                : null,
            'hora_inicio'          => $a->hora_inicio,
            'hora_fim'             => $a->hora_fim,
            'plantao'              => (bool) $a->plantao,
            'medico'               => $a->medico ? $this->formatarMedico($a->medico) : null,
        ];
    }

    /**
     * @OA\Get(path="/agenda", tags={"Agenda"}, summary="Listar agenda",
     *     description="Retorna os plantões cadastrados. Consumido pelo Grupo 2 para verificar disponibilidade.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id_medico", in="query", @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="data",      in="query", @OA\Schema(type="string", format="date", example="2026-04-15")),
     *     @OA\Response(response=200, description="Lista de plantões"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function index(): JsonResponse
    {
        $agenda = Agenda::with(['medico.pessoa'])
            ->when(request('id_medico'), fn($q) => $q->where('id_medico', request('id_medico')))
            ->when(request('data'),      fn($q) => $q->where('data_disponibilidade', request('data')))
            ->orderBy('data_disponibilidade')
            ->orderBy('hora_inicio')
            ->get()
            ->map(fn($a) => $this->formatarAgenda($a));

        return response()->json($agenda);
    }

    /**
     * @OA\Get(path="/medicos/{id}/agenda", tags={"Agenda"}, summary="Agenda de um médico",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id",   in="path",  required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="data", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Agenda do médico"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function porMedico(int $id): JsonResponse
    {
        $medico = Medico::with('pessoa')->findOrFail($id);

        $agenda = $medico->agenda()
            ->when(request('data'), fn($q) => $q->where('data_disponibilidade', request('data')))
            ->orderBy('data_disponibilidade')
            ->orderBy('hora_inicio')
            ->get()
            ->map(fn($a) => [
                'id'                   => $a->id,
                'data_disponibilidade' => $a->data_disponibilidade
                    ? (is_string($a->data_disponibilidade) ? $a->data_disponibilidade : $a->data_disponibilidade->format('Y-m-d'))
                    : null,
                'hora_inicio'          => $a->hora_inicio,
                'hora_fim'             => $a->hora_fim,
                'plantao'              => (bool) $a->plantao,
            ]);

        return response()->json([
            'medico' => $this->formatarMedico($medico),
            'agenda' => $agenda,
        ]);
    }

    /**
     * @OA\Post(path="/agenda", tags={"Agenda"}, summary="Criar plantão",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"id_medico","data_disponibilidade","hora_inicio","hora_fim"},
     *             @OA\Property(property="id_medico",            type="integer", example=1),
     *             @OA\Property(property="data_disponibilidade", type="string",  format="date", example="2026-04-15"),
     *             @OA\Property(property="hora_inicio",          type="string",  example="08:00"),
     *             @OA\Property(property="hora_fim",             type="string",  example="18:00"),
     *             @OA\Property(property="plantao",              type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Plantão criado"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function store(AgendaRequest $request): JsonResponse
    {
        $agenda = Agenda::create($request->validated());
        $agenda->load('medico.pessoa');

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar(
            $logado,
            "Usuário {$logado->usuario} criou plantão para {$agenda->medico->pessoa?->nome} em {$request->data_disponibilidade} das {$request->hora_inicio} às {$request->hora_fim}."
        );

        return response()->json([
            'mensagem' => 'Plantão cadastrado com sucesso.',
            'agenda'   => $this->formatarAgenda($agenda),
        ], 201);
    }

    /**
     * @OA\Get(path="/agenda/{id}", tags={"Agenda"}, summary="Buscar plantão por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Dados do plantão"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $agenda = Agenda::with('medico.pessoa')->findOrFail($id);
        return response()->json($this->formatarAgenda($agenda));
    }

    /**
     * @OA\Put(path="/agenda/{id}", tags={"Agenda"}, summary="Atualizar plantão",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Plantão atualizado")
     * )
     */
    public function update(AgendaRequest $request, int $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->update($request->validated());
        $agenda->load('medico.pessoa');

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($logado, "Usuário {$logado->usuario} atualizou o plantão ID {$id}.");

        return response()->json([
            'mensagem' => 'Plantão atualizado com sucesso.',
            'agenda'   => $this->formatarAgenda($agenda),
        ]);
    }

    /**
     * @OA\Delete(path="/agenda/{id}", tags={"Agenda"}, summary="Remover plantão",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Plantão removido")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($logado, "Usuário {$logado->usuario} removeu o plantão ID {$id}.");

        return response()->json(['mensagem' => 'Plantão removido com sucesso.']);
    }
}
