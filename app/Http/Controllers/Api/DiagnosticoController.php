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

    // View: detalhes de diagnóstico (documentação OpenAPI mantida nas rotas API)
    public function mostrar(int $id)
    {
        $diagnostico = Diagnostico::with('consulta')->findOrFail($id);
        return view('prontuario.diagnosticoDetalhes', compact('diagnostico'));
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
    public function index(int $idConsulta): JsonResponse
    {
        $diagnosticos = Diagnostico::where('id_consulta', $idConsulta)->get();
        return response()->json(['success' => true, 'data' => $diagnosticos]);
    }

    /**
     * @OA\POST(
     *     path="/api/consultas/{idConsulta}/diagnosticos",
     *     tags={"Diagnóstico"},
     *     summary="Criar um novo diagnóstico",
     *     description="Cria um novo diagnóstico para uma consulta específica com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Diagnostico")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Diagnóstico criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Diagnostico")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/consultas/{idConsulta}/diagnosticos/{id}",
     *     tags={"Diagnóstico"},
     *     summary="Obter detalhes de um diagnóstico",
     *     description="Retorna os detalhes de um diagnóstico específico com base no ID fornecido.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diagnóstico não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
    public function show(int $idConsulta, int $id): JsonResponse
    {
        $diagnostico = Diagnostico::where('id_consulta', $idConsulta)->findOrFail($id);
        return response()->json(['success' => true, 'data' => $diagnostico]);
    }

    /**
     * @OA\Put(
     *     path="/api/consultas/{idConsulta}/diagnosticos/{id}",
     *     tags={"Diagnóstico"},
     *     summary="Atualizar um diagnóstico existente",
     *     description="Atualiza um diagnóstico específico para uma consulta com base nos IDs fornecidos.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do diagnóstico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Diagnostico")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diagnóstico atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Diagnostico")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diagnóstico não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/consultas/{idConsulta}/diagnosticos/{id}",
     *     tags={"Diagnóstico"},
     *     summary="Remover um diagnóstico",
     *     description="Remove um diagnóstico específico de uma consulta com base nos IDs fornecidos.",
     *     @OA\Parameter(
     *         name="idConsulta",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do diagnóstico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diagnóstico removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Diagnóstico removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diagnóstico não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/RespostaErro")
     *     )
     * )
     */
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
