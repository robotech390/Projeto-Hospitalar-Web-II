<?php

namespace App\Http\Controllers;

use App\Models\HistoricoLog;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Histórico de ações do sistema.
 *
 * Todos os grupos podem registrar logs via POST /api/logs,
 * informando apenas a descrição da ação executada.
 */
class LogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/logs",
     *     tags={"Logs"},
     *     summary="Listar logs",
     *     description="Retorna os últimos registros do histórico de ações, ordenados do mais recente para o mais antigo.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id_usuario", in="query", @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="limite",     in="query", @OA\Schema(type="integer", example=100)),
     *     @OA\Response(response=200, description="Lista de logs"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function index(): JsonResponse
    {
        $logs = HistoricoLog::with(['usuario:id,usuario,email,funcao'])
            ->when(request('id_usuario'), fn($q) => $q->where('id_usuario', request('id_usuario')))
            ->orderByDesc('data')
            ->limit((int) request('limite', 100))
            ->get();

        return response()->json($logs);
    }

    /**
     * @OA\Post(
     *     path="/logs",
     *     tags={"Logs"},
     *     summary="Registrar log de ação",
     *     description="Endpoint que outras equipes utilizam para registrar uma ação no histórico. O usuário autenticado pelo token é automaticamente associado ao log.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"descricao"},
     *             @OA\Property(property="descricao", type="string", example="Paciente João Silva realizou check-in para a consulta de Cardiologia.")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Log registrado com sucesso"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'descricao' => ['required', 'string', 'max:5000'],
        ], [
            'descricao.required' => 'Informe a descrição da ação.',
            'descricao.max'      => 'A descrição é muito longa (máximo 5000 caracteres).',
        ]);

        $usuario = JWTAuth::parseToken()->authenticate();
        LogService::registrar($usuario, $request->descricao);

        return response()->json([
            'mensagem' => 'Log registrado com sucesso.',
        ], 201);
    }
}
