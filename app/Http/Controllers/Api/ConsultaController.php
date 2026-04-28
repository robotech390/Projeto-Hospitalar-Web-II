<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Http\Requests\ConsultaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    // GET /api/consultas
    public function index(Request $request): JsonResponse
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
    }

    // POST /api/consultas
    public function store(ConsultaRequest $request): JsonResponse
    {
        $consulta = Consulta::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Consulta criada com sucesso.',
            'data'    => $consulta,
        ], 201);
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
    public function destroy(int $id): JsonResponse
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Consulta removida com sucesso.',
        ]);
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
