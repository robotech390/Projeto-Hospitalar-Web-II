<?php

namespace App\Http\Controllers\Recepcao;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\TipoConsulta;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgendamentoController extends Controller
{
    /**
     * @OA\Get(
     *   path="/recepcao/agendamento",
     *   tags={"Agendamento"},
     *   summary="Lista consultas do mês atual e tipos de consulta",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Dados de agendamento do mês atual",
     *     @OA\JsonContent(
     *       @OA\Property(property="consultas", type="array",
     *         @OA\Items(
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="data", type="string", format="date"),
     *           @OA\Property(property="hora_inicio", type="string", example="08:00"),
     *           @OA\Property(property="hora_fim", type="string", example="08:30"),
     *           @OA\Property(property="status", type="string", example="agendado"),
     *           @OA\Property(property="descricao", type="string")
     *         )
     *       ),
     *       @OA\Property(property="tiposConsulta", type="array",
     *         @OA\Items(
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="nome", type="string")
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function index()
    {
        $consultas = Consulta::with(['paciente.pessoa', 'medico.pessoa', 'tipoConsulta'])
            ->whereYear('data', now()->year)
            ->whereMonth('data', now()->month)
            ->get();

        $tiposConsulta = TipoConsulta::all();

        return Inertia::render('Recepcao/Agendamento', [
            'consultas' => $consultas,
            'tiposConsulta' => $tiposConsulta,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/recepcao/medicos",
     *   tags={"Agendamento"},
     *   summary="Lista médicos ativos",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Lista de médicos",
     *     @OA\JsonContent(type="array",
     *       @OA\Items(
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="nome", type="string"),
     *         @OA\Property(property="especialidade", type="string")
     *       )
     *     )
     *   )
     * )
     */
    public function medicos()
    {
        $medicos = Medico::with('pessoa')
            ->where('status', 'ativo')
            ->get()
            ->map(fn($m) => [
                'id'           => $m->id,
                'nome'         => $m->pessoa->nome,
                'especialidade' => $m->especialidade,
            ]);

        return response()->json($medicos);
    }

    /**
     * @OA\Get(
     *   path="/recepcao/pacientes",
     *   tags={"Agendamento"},
     *   summary="Lista pacientes",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Lista de pacientes",
     *     @OA\JsonContent(type="array",
     *       @OA\Items(
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="nome", type="string"),
     *         @OA\Property(property="cpf", type="string")
     *       )
     *     )
     *   )
     * )
     */
    public function pacientes()
    {
        $pacientes = Usuario::with('pessoa')
            ->where('funcao', 'paciente')
            ->get()
            ->map(fn($u) => [
                'id'   => $u->id,
                'nome' => $u->pessoa->nome,
                'cpf'  => $u->pessoa->cpf,
            ]);

        return response()->json($pacientes);
    }

    /**
     * @OA\Get(
     *   path="/recepcao/disponibilidade",
     *   tags={"Agendamento"},
     *   summary="Retorna slots de horário disponíveis para um médico em uma data",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="medico_id",
     *     in="query",
     *     required=true,
     *     description="ID do médico",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Parameter(
     *     name="data",
     *     in="query",
     *     required=true,
     *     description="Data no formato YYYY-MM-DD",
     *     @OA\Schema(type="string", format="date", example="2026-05-20")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Lista de slots de horário",
     *     @OA\JsonContent(type="array",
     *       @OA\Items(
     *         @OA\Property(property="hora", type="string", example="08:00"),
     *         @OA\Property(property="disponivel", type="boolean", example=true)
     *       )
     *     )
     *   ),
     *   @OA\Response(response=422, description="Parâmetros inválidos")
     * )
     */
    public function disponibilidade(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|integer',
            'data'      => 'required|date',
        ]);

        $agendas = Agenda::where('id_medico', $request->medico_id)
            ->where('data_disponibilidade', $request->data)
            ->get();

        $ocupados = Consulta::where('id_medico', $request->medico_id)
            ->where('data', $request->data)
            ->where('status', '!=', 'cancelado')
            ->pluck('hora_inicio')
            ->map(fn($h) => substr($h, 0, 5))
            ->toArray();

        $slots = [];

        foreach ($agendas as $agenda) {
            $current = strtotime($agenda->hora_inicio);
            $fim     = strtotime($agenda->hora_fim);

            while ($current < $fim) {
                $hora = date('H:i', $current);
                $slots[] = [
                    'hora'      => $hora,
                    'disponivel' => !in_array($hora, $ocupados),
                ];
                $current += 30 * 60;
            }
        }

        return response()->json($slots);
    }

    /**
     * @OA\Post(
     *   path="/recepcao/agendamento",
     *   tags={"Agendamento"},
     *   summary="Cria novo agendamento",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"id_paciente","id_medico","id_tipo_consulta","data","hora_inicio"},
     *       @OA\Property(property="id_paciente", type="integer", example=1),
     *       @OA\Property(property="id_medico", type="integer", example=2),
     *       @OA\Property(property="id_tipo_consulta", type="integer", example=1),
     *       @OA\Property(property="data", type="string", format="date", example="2026-05-20"),
     *       @OA\Property(property="hora_inicio", type="string", example="09:00"),
     *       @OA\Property(property="descricao", type="string", nullable=true, example="Consulta de rotina")
     *     )
     *   ),
     *   @OA\Response(response=302, description="Redirecionamento após criação bem-sucedida"),
     *   @OA\Response(response=422, description="Dados inválidos ou horário já ocupado")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_paciente'      => 'required|integer',
            'id_medico'        => 'required|integer',
            'id_tipo_consulta' => 'required|integer',
            'data'             => 'required|date',
            'hora_inicio'      => 'required',
            'descricao'        => 'nullable|string',
        ]);

        $conflito = Consulta::where('id_medico', $request->id_medico)
            ->where('data', $request->data)
            ->where('hora_inicio', $request->hora_inicio)
            ->where('status', '!=', 'cancelado')
            ->exists();

        if ($conflito) {
            return back()->withErrors(['hora_inicio' => 'Horário já ocupado.']);
        }

        $horaFim = date('H:i', strtotime($request->hora_inicio) + 30 * 60);

        Consulta::create([
            'id_paciente'      => $request->id_paciente,
            'id_medico'        => $request->id_medico,
            'id_tipo_consulta' => $request->id_tipo_consulta,
            'data'             => $request->data,
            'hora_inicio'      => $request->hora_inicio,
            'hora_fim'         => $horaFim,
            'status'           => 'agendado',
            'descricao'        => $request->descricao,
        ]);

        return redirect()->route('recepcao.agendamento');
    }

    public function edit($id)
    {
        $consulta = Consulta::with(['paciente.pessoa', 'medico.pessoa', 'tipoConsulta'])
            ->findOrFail($id);

        return response()->json($consulta);
    }

    /**
     * @OA\Put(
     *   path="/recepcao/agendamento/{id}",
     *   tags={"Agendamento"},
     *   summary="Atualiza uma consulta existente",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID da consulta",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"id_paciente","id_medico","id_tipo_consulta","data","hora_inicio"},
     *       @OA\Property(property="id_paciente", type="integer", example=1),
     *       @OA\Property(property="id_medico", type="integer", example=2),
     *       @OA\Property(property="id_tipo_consulta", type="integer", example=1),
     *       @OA\Property(property="data", type="string", format="date", example="2026-05-20"),
     *       @OA\Property(property="hora_inicio", type="string", example="09:00"),
     *       @OA\Property(property="descricao", type="string", nullable=true, example="Consulta de rotina"),
     *       @OA\Property(property="status", type="string", nullable=true, enum={"agendado","cancelado","realizado"}, example="agendado")
     *     )
     *   ),
     *   @OA\Response(response=302, description="Redirecionamento após atualização bem-sucedida"),
     *   @OA\Response(response=404, description="Consulta não encontrada"),
     *   @OA\Response(response=422, description="Dados inválidos ou horário já ocupado")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_paciente'      => 'required|integer',
            'id_medico'        => 'required|integer',
            'id_tipo_consulta' => 'required|integer',
            'data'             => 'required|date',
            'hora_inicio'      => 'required',
            'descricao'        => 'nullable|string',
            'status'           => 'nullable|string|in:agendado,cancelado,realizado',
        ]);

        $conflito = Consulta::where('id_medico', $request->id_medico)
            ->where('data', $request->data)
            ->where('hora_inicio', $request->hora_inicio)
            ->where('status', '!=', 'cancelado')
            ->where('id', '!=', $id)
            ->exists();

        if ($conflito) {
            return back()->withErrors(['hora_inicio' => 'Horário já ocupado.']);
        }

        $consulta = Consulta::findOrFail($id);

        $horaFim = date('H:i', strtotime($request->hora_inicio) + 30 * 60);

        $consulta->update([
            'id_paciente'      => $request->id_paciente,
            'id_medico'        => $request->id_medico,
            'id_tipo_consulta' => $request->id_tipo_consulta,
            'data'             => $request->data,
            'hora_inicio'      => $request->hora_inicio,
            'hora_fim'         => $horaFim,
            'descricao'        => $request->descricao,
            'status'           => $request->status ?? $consulta->status,
        ]);

        return redirect()->route('recepcao.agendamento');
    }

    /**
     * @OA\Delete(
     *   path="/recepcao/agendamento/{id}",
     *   tags={"Agendamento"},
     *   summary="Cancela uma consulta (soft delete via status)",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID da consulta a cancelar",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(response=302, description="Redirecionamento após cancelamento"),
     *   @OA\Response(response=404, description="Consulta não encontrada")
     * )
     */
    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->status = 'cancelado';
        $consulta->save();

        return redirect()->route('recepcao.agendamento');
    }
}
