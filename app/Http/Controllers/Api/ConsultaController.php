<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Http\Requests\ConsultaRequest;
use Illuminate\Http\JsonResponse;
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

    // View: detalhes de consulta
    public function mostrar(int $id)
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);
        $tipos_consulta = TipoConsulta::all();
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $medicos = Medico::with('pessoa')->get();

        return view('prontuario.consultaForm', compact('consulta', 'tipos_consulta', 'pacientes', 'medicos'));
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

}
