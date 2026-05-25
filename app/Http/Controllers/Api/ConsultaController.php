<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Http\Requests\ConsultaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\TipoConsulta;
use App\Models\Pessoa;
use App\Models\Medico;

//id, descricao(varchar), data(date), hora_inicio(time), hora_fim(time), data_check_in(datetime), status(varchar), id_tipo_consulta(int), id_paciente(int), id_medico(int)
class ConsultaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/consultas",
     *     tags={"Consulta"},
     *     summary="Listar todas as consultas",
     *     description="Retorna uma lista de todas as consultas registradas no sistema, incluindo detalhes como diagnóstico, receitas e solicitações de exame.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de consultas retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="descricao", type="string", example="Consulta de rotina"),
     *                     @OA\Property(property="data", type="string", format="date", example="2024-06-01"),
     *                     @OA\Property(property="hora_inicio", type="string", format="time", example="14:00:00"),
     *                     @OA\Property(property="hora_fim", type="string", format="time", example="14:30:00"),
     *                     @OA\Property(property="data_check_in", type="string", format="date-time", example="2024-06-01T13:45:00Z"),
     *                     @OA\Property(property="status", type="string", example="em_espera"),
     *                     @OA\Property(property="id_tipo_consulta", type="integer", example=2),
     *                     @OA\Property(property="id_paciente", type="integer", example=5),
     *                     @OA\Property(property="id_medico", type="integer", example=3),
     *                     // Adicione propriedades para relacionamentos, como diagnosticos, receitas, etc.
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
    */
    public function index(): JsonResponse
    {
        $consultas = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame', 'medico.pessoa', 'paciente', 'tipo_consulta'])->get();
        return response()->json(['success' => true, 'data' => $consultas]);
    }

    public function lista()
    {
        $consultas = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame', 'medico.pessoa', 'paciente', 'tipo_consulta'])->get();
        return view('prontuario.consultas', compact('consultas'));
    }

    // View: formulário de criação/edição de consulta (documentação OpenAPI mantida nas rotas API)
    public function formulario(?int $consultaId = null)
    {
        $tipos_consulta = TipoConsulta::all();
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $medicos = Medico::with('pessoa')->get();
        $selectedConsulta = $consultaId;
        return view('prontuario.consultaForm', compact('tipos_consulta', 'pacientes', 'medicos', 'selectedConsulta'));
    }

    public function salvar(ConsultaRequest $request)
    {
        $data = $request->validated();
        $data['data_criacao'] = now();
        if (isset($data['hora_inicio'])) {
            $data['hora_inicio'] = $data['data'] . ' ' . $data['hora_inicio'] . ':00';
        }
        if (isset($data['hora_fim'])) {
            $data['hora_fim'] = $data['data'] . ' ' . $data['hora_fim'] . ':00';
        }
        $consulta = Consulta::create($data);

        return redirect('/consultas')->with('success', 'Consulta criada com sucesso.');
    }

    // View: formulário de edição de consulta (documentação OpenAPI mantida nas rotas API)
    public function editar(int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $tipos_consulta = TipoConsulta::all();
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $medicos = Medico::with('pessoa')->get();
        
        return view('prontuario.consultaForm', compact('consulta', 'tipos_consulta', 'pacientes', 'medicos'));
    }

    public function atualizar(ConsultaRequest $request, int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->validated());

        return redirect()->route('consultas.index')->with('success', 'Consulta atualizada com sucesso.');
    }

    public function remover(int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return redirect()->route('consultas.index')->with('success', 'Consulta deletada com sucesso.');
    }

    /**
     * @OA\Post(
     *     path="/consultas",
     *     tags={"Consulta"},
     *     summary="Criar uma nova consulta",
     *     description="Cria uma nova consulta com os dados fornecidos no corpo da requisição. Os dados devem incluir descrição, data, hora de início, hora de fim, status, tipo de consulta, paciente e médico associados.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="descricao", type="string", example="Consulta de rotina"),
     *             @OA\Property(property="data", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="14:00:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="15:00:00"),
     *             @OA\Property(property="status", type="string", example="agendada"),
     *             @OA\Property(property="id_tipo_consulta", type="integer", example=1),
     *             @OA\Property(property="id_paciente", type="integer", example=5),
     *             @OA\Property(property="id_medico", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Consulta criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function store(ConsultaRequest $request)
    {
        $data = $request->validated();
        $data['data_criacao'] = now();
        if (isset($data['hora_inicio'])) {
            $data['hora_inicio'] = $data['data'] . ' ' . $data['hora_inicio'] . ':00';
        }
        if (isset($data['hora_fim'])) {
            $data['hora_fim'] = $data['data'] . ' ' . $data['hora_fim'] . ':00';
        }
        $consulta = Consulta::create($data);

        return response()->json(['success' => true, 'data' => $consulta], 201);
    }

    public function mostrar(int $id)
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);

        return view('prontuario.consultaForm', compact('consulta'));
    }

    /**
     * @OA\Get(
     *     path="/api/consultas/{id}",
     *     tags={"Consulta"},
     *     summary="Obter detalhes de uma consulta",
     *     description="Retorna os detalhes de uma consulta específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consulta encontrada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consulta não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $consulta]);
    }

    /**
     * @OA\Put(
     *     path="/consultas/{id}",
     *     tags={"Consulta"},
     *     summary="Atualizar uma consulta existente",
     *     description="Atualiza os detalhes de uma consulta existente com base no ID fornecido e nos dados fornecidos no corpo da requisição.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",         description="ID da consulta a ser atualizada",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consulta atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function update(ConsultaRequest $request, int $id): JsonResponse
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->validated());

        return response()->json(['success' => true, 'data' => $consulta]);
    }

    /**
     * @OA\Delete(
     *     path="/consultas/{id}",
     *     tags={"Consulta"},
     *     summary="Excluir uma consulta",
     *     description="Exclui uma consulta específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da consulta a ser excluída",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consulta excluída com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consulta não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return response()->json(['success' => true, 'message' => 'Consulta deletada com sucesso.']);
    }

    /**
     * @OA\Get(
     *     path="/consultas/fila/hoje",
     *     tags={"Consulta"},
     *     summary="Obter fila de consultas do dia",
     *     description="Retorna pacientes que fizeram check-in no dia (painel do médico)",
     *     @OA\Parameter(
     *         name="id_medico",
     *         in="query",
     *         description="ID do médico",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consulta atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     ),
     * )
     */
    public function fila(Request $request): JsonResponse
    {
        $query = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame'])
            ->whereDate('data', now()->toDateString())
            ->whereNotNull('data_check_in')
            ->where('status', 'em_espera');

        if ($request->filled('id_medico')) {
            $query->where('id_medico', $request->id_medico);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('data_check_in')->get(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pacientes/{idPaciente}/historico",
     *     tags={"Consulta"},
     *     summary="Obter histórico de consultas de um paciente",
     *     description="Retorna o histórico completo de consultas de um paciente específico",
     *     @OA\Parameter(
     *         name="idPaciente",
     *         in="path",
     *         description="ID do paciente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Histórico de consultas retornado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Consulta")
     *     )
     * )
     */
    public function historico(int $idPaciente): JsonResponse
    {
        $consultas = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])
            ->where('id_paciente', $idPaciente)
            ->where('status', 'concluida')
            ->orderByDesc('data')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $consultas,
        ]);
    }

    /**
     * Obter agendamentos de um paciente (leitura do Grupo 2)
     */
    public function agendamentosPaciente(int $idPaciente): JsonResponse
    {
        $agendamentos = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame', 'medico.pessoa'])
            ->where('id_paciente', $idPaciente)
            ->where(function ($q) {
                $q->where('status', 'agendada')
                  ->orWhereDate('data', '>=', now()->toDateString());
            })
            ->orderBy('data')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $agendamentos,
        ]);
    }
}
