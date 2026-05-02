<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnostico;
use App\Http\Requests\DiagnosticoRequest;
use Illuminate\Http\JsonResponse;

class DiagnosticoController extends Controller
{
    // GET /api/consultas/{idConsulta}/diagnosticos
    public function index(int $idConsulta): JsonResponse
    {
        $diagnosticos = Diagnostico::where('id_consulta', $idConsulta)->get();

        return response()->json(['success' => true, 'data' => $diagnosticos]);
    }

    // POST /api/consultas/{idConsulta}/diagnosticos
    public function store(DiagnosticoRequest $request, int $idConsulta): JsonResponse
    {
        $diagnostico = Diagnostico::create(
            array_merge($request->validated(), ['id_consulta' => $idConsulta])
        );

        return response()->json([
            'success' => true,
            'message' => 'Diagnóstico registrado com sucesso.',
            'data'    => $diagnostico,
        ], 201);
    }

    // GET /api/consultas/{idConsulta}/diagnosticos/{id}
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $diagnostico = Diagnostico::where('id_consulta', $idConsulta)->findOrFail($id);

        return response()->json(['success' => true, 'data' => $diagnostico]);
    }

    // PUT /api/consultas/{idConsulta}/diagnosticos/{id}
    public function update(DiagnosticoRequest $request, int $idConsulta, int $id): JsonResponse
    {
        $diagnostico = Diagnostico::where('id_consulta', $idConsulta)->findOrFail($id);
        $diagnostico->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Diagnóstico atualizado com sucesso.',
            'data'    => $diagnostico,
        ]);
    }

    // DELETE /api/consultas/{idConsulta}/diagnosticos/{id}
    public function destroy(int $idConsulta, int $id): JsonResponse
    {
        $diagnostico = Diagnostico::where('id_consulta', $idConsulta)->findOrFail($id);
        $diagnostico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diagnóstico removido com sucesso.',
        ]);
    }
}
