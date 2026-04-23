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

    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->status = 'cancelado';
        $consulta->save();

        return redirect()->route('recepcao.agendamento');
    }
}
