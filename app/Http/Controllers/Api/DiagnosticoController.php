<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Http\Requests\DiagnosticoRequest;
use Illuminate\Http\JsonResponse;

class DiagnosticoController extends Controller
{
    // ========== MÉTODOS WEB ==========

    // View: lista de diagnósticos (documentação OpenAPI mantida nas rotas API)
    public function lista()
    {
        $diagnosticos = Diagnostico::with('consulta')->get();
        return view('prontuario.diagnosticos', compact('diagnosticos'));
    }

    // View: formulário de diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function formulario(?int $consultaId = null)
    {
        $consultas = Consulta::all();
        $selectedConsulta = $consultaId;
        return view('prontuario.diagnosticoForm', compact('consultas', 'selectedConsulta'));
    }

    // Web: salvar diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function salvar(DiagnosticoRequest $request)
    {
        Diagnostico::create($request->validated());
        return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico criado com sucesso.');
    }

    // View: editar diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function editar(int $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $consultas = Consulta::all();
        return view('prontuario.diagnosticoForm', compact('diagnostico', 'consultas'));
    }

    // Web: atualizar diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function atualizar(DiagnosticoRequest $request, int $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $diagnostico->update($request->validated());
        return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico atualizado com sucesso.');
    }

    // Web: remover diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function remover(int $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $diagnostico->delete();
        return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico deletado com sucesso.');
    }

    /**
     * @OA\Get(
     *     path="/api/diagnosticos",
     *     tags={"Diagnóstico"},
     *     summary="Listar todos os diagnósticos",
     *     description="Retorna todas as entradas de diagnóstico do sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de diagnósticos retornada com sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Diagnostico"))
     *     )
     * )
     */
    public function all(): JsonResponse
    {
        $diagnosticos = Diagnostico::with('consulta')->get();

        return response()->json(['success' => true, 'data' => $diagnosticos]);
    }

    /**
     * @OA\Get(
     *     path="/api/diagnosticos/{id}",
     *     tags={"Diagnóstico"},
     *     summary="Obter detalhes de um diagnóstico",
     *     description="Retorna os detalhes de um diagnóstico específico com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do diagnóstico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diagnóstico encontrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Diagnostico")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $diagnostico = Diagnostico::with('consulta')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $diagnostico]);
    }
}
