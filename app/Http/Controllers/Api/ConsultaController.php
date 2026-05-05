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
    // GET /api/consultas
    public function index(){
        $consultas = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame', 'medico.pessoa', 'paciente'])->get();
        return view('prontuario.consultas', compact('consultas'));
    }

    // GET /api/consultas/form
    public function create(){
        $tipos_consulta = TipoConsulta::all();
        //Exclui pessoas que são médicos da lista de pacientes
        $medicoPessoaIds = Medico::pluck('id_pessoa');//pluck retorna uma coleção de IDs de pessoas que são médicos
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();//Retorna pessoas que não são médicos, ou seja, pacientes
        $medicos = Medico::with('pessoa')->get();//Retorna médicos com seus dados de pessoa relacionados
        return view('prontuario.consultaForm', compact('tipos_consulta', 'pacientes', 'medicos'));
    }

    // GET /api/consultas/{id}/edit
    public function edit(int $id){
        $consulta = Consulta::findOrFail($id);
        $tipos_consulta = TipoConsulta::all();
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $medicos = Medico::with('pessoa')->get();
        
        return view('prontuario.consultaForm', compact('consulta', 'tipos_consulta', 'pacientes', 'medicos'));
    }

    // POST /api/consultas
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

    // GET /api/consultas/{id}
    public function show(int $id)
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);

        return view('consultas.index', compact('consulta'));
    }

    // PUT /api/consultas/{id}
    public function update(ConsultaRequest $request, int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->validated());

        return redirect()->route('consultas.index')->with('success', 'Consulta atualizada com sucesso.');
    }

    // DELETE /api/consultas/{id}
    public function destroy(int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return redirect()->route('consultas.index')->with('success', 'Consulta deletada com sucesso.');
    }

    // GET /api/consultas/fila/hoje?id_medico=X
    // Retorna pacientes que fizeram check-in no dia (painel do médico)
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

    // GET /api/pacientes/{idPaciente}/historico
    // Histórico completo de consultas de um paciente
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
