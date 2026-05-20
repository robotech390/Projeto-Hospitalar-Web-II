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
    public function index(){
        $consultas = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame', 'medico.pessoa', 'paciente'])->get();
        return view('prontuario.consultas', compact('consultas'));
    }

    /** 
     * @OA\Get(
     *     path="/consultas/create",
     *     tags={"Consulta"},
     *     summary="Exibir formulário de criação de consulta",
     *     description="Retorna os dados necessários para exibir o formulário de criação de uma nova consulta, incluindo os tipos de consulta disponíveis, lista de pacientes e médicos.",
     *     @OA\Response(
     *         response=200,
     *         description="Dados para formulário de criação de consulta retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="tipos_consulta",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="descricao", type="string", example="Consulta de rotina")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pacientes",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="nome", type="string", example="João Silva")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="medicos",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="nome", type="string", example="Dra. Maria Oliveira")
     *                     )
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
    public function create(){
        $tipos_consulta = TipoConsulta::all();
        //Exclui pessoas que são médicos da lista de pacientes
        $medicoPessoaIds = Medico::pluck('id_pessoa');//pluck retorna uma coleção de IDs de pessoas que são médicos
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();//Retorna pessoas que não são médicos, ou seja, pacientes
        $medicos = Medico::with('pessoa')->get();//Retorna médicos com seus dados de pessoa relacionados
        return view('prontuario.consultaForm', compact('tipos_consulta', 'pacientes', 'medicos'));
    }

    /**
     * @OA\Get(
     *     path="/consultas/{id}/edit",
     *     tags={"Consulta"},
     *     summary="Exibir formulário de edição de consulta",
     *     description="Retorna os dados necessários para exibir o formulário de edição de uma consulta existente, incluindo os detalhes da consulta a ser editada, tipos de consulta disponíveis, lista de pacientes e médicos.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da consulta a ser editada",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados para formulário de edição de consulta retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="consulta",
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
     *                     @OA\Property(property="id_medico", type="integer", example=3)
     *                 ),
     *                 @OA\Property(
     *                     property="tipos_consulta",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="descricao", type="string", example="Consulta de rotina")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pacientes",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="nome", type="string", example="João Silva")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="medicos",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="nome", type="string", example="Dra. Maria Oliveira")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consulta não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")     *     )
     * )
     */
    public function edit(int $id){
        $consulta = Consulta::findOrFail($id);
        $tipos_consulta = TipoConsulta::all();
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $medicos = Medico::with('pessoa')->get();
        
        return view('prontuario.consultaForm', compact('consulta', 'tipos_consulta', 'pacientes', 'medicos'));
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

        return redirect('/consultas')->with('success', 'Consulta criada com sucesso.');
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
    public function show(int $id)
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);

        return view('consultas.index', compact('consulta'));
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
    public function update(ConsultaRequest $request, int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->validated());

        return redirect()->route('consultas.index')->with('success', 'Consulta atualizada com sucesso.');
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
    public function destroy(int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return redirect()->route('consultas.index')->with('success', 'Consulta deletada com sucesso.');
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
}
