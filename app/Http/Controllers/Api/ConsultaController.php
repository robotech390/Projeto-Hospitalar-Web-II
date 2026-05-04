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
    public function index(){
        $consultas = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame'])->get();
        return view('prontuario.consultas', compact('consultas'));
    }
    // GET /api/consultas
    /*public function index(Request $request): JsonResponse
    {
        $query = Consulta::with(['diagnosticos', 'receitas', 'solicitacoesExame']);

        if ($request->filled('id_paciente')) {
            $query->where('id_paciente', $request->id_paciente);
        }
        if ($request->filled('id_medico')) {
            $query->where('id_medico', $request->id_medico);
        }
        if ($request->filled('data')) {
            $query->whereDate('data', $request->data);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $consultas = $query->orderBy('data')->orderBy('hora_inicio')->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $consultas->items(),
            'meta'    => [
                'total'        => $consultas->total(),
                'per_page'     => $consultas->perPage(),
                'current_page' => $consultas->currentPage(),
                'last_page'    => $consultas->lastPage(),
            ],
        ]);
    }*/

    public function create(){
        $tipos_consulta = TipoConsulta::all();
        $pacientes = Pessoa::all();
        $medicos = Medico::with('pessoa')->get();
        return view('prontuario.consultaForm', compact('tipos_consulta', 'pacientes', 'medicos'));
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
    public function show(int $id): JsonResponse
    {
        $consulta = Consulta::with([
            'diagnosticos',
            'receitas.medicamentos',
            'solicitacoesExame.itens',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $consulta,
        ]);
    }

    // PUT /api/consultas/{id}
    public function update(ConsultaRequest $request, int $id): JsonResponse
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Consulta atualizada com sucesso.',
            'data'    => $consulta,
        ]);
    }

    // DELETE /api/consultas/{id}
    public function destroy(int $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return view('prontuario.consultas')->with('success', 'Consulta deletada com sucesso.');
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
