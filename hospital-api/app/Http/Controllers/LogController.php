<?php

namespace App\Http\Controllers;

use App\Models\HistoricoLog;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Gerenciamento do histórico de logs.
 *
 * Todas as equipes devem:
 *  1. Validar o token JWT via GET /api/auth/me
 *  2. Registrar suas ações via POST /api/logs
 *
 * O administrador pode consultar o histórico via GET /api/logs.
 */
class LogController extends Controller
{
    // ─── Listar logs ──────────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/logs",
     *     tags={"Logs"},
     *     summary="Listar histórico de logs",
     *     description="Retorna o histórico de ações do sistema. Pode ser filtrado por usuário e/ou período.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id_usuario", in="query", required=false,
     *         description="Filtra logs de um usuário específico",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(name="data_inicio", in="query", required=false,
     *         description="Data de início do filtro (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-04-01")
     *     ),
     *     @OA\Parameter(name="data_fim", in="query", required=false,
     *         description="Data de fim do filtro (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2026-04-30")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de logs",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id",         type="integer",  example=1),
     *             @OA\Property(property="log",        type="string",   example="Usuário João criou um agendamento às 14:00 do dia 14/02/2026."),
     *             @OA\Property(property="data_hora",  type="string",   example="2026-02-14 14:00:00"),
     *             @OA\Property(property="usuario", type="object",
     *                 @OA\Property(property="id",     type="integer", example=1),
     *                 @OA\Property(property="nome",   type="string",  example="João Silva"),
     *                 @OA\Property(property="funcao", type="string",  example="recepcionista")
     *             )
     *         ))
     *     ),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function index(): JsonResponse
    {
        $idUsuario  = request('id_usuario');
        $dataInicio = request('data_inicio');
        $dataFim    = request('data_fim');

        $logs = HistoricoLog::with(['usuario:id,usuario,funcao'])
            ->when($idUsuario,  fn($q) => $q->where('id_usuario', $idUsuario))
            ->when($dataInicio, fn($q) => $q->where('data', '>=', $dataInicio . ' 00:00:00'))
            ->when($dataFim,    fn($q) => $q->where('data', '<=', $dataFim    . ' 23:59:59'))
            ->orderByDesc('data')
            ->paginate(50);

        return response()->json($logs);
    }

    // ─── Receber log de outra equipe ──────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/logs",
     *     tags={"Logs"},
     *     summary="Registrar log (endpoint para outras equipes)",
     *     description="Permite que outras equipes registrem ações dos seus sistemas no log central. O usuário deve estar autenticado via token JWT.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"descricao"},
     *             @OA\Property(property="descricao", type="string",
     *                 example="Paciente Maria Santos (ID 42) realizou check-in às 09:30 do dia 15/04/2026.",
     *                 description="Descrição legível da ação realizada. Inclua nome do usuário, ação e data/hora para fácil leitura.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Log registrado com sucesso",
     *         @OA\JsonContent(@OA\Property(property="mensagem", type="string", example="Log registrado com sucesso."))
     *     ),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'descricao' => ['required', 'string', 'max:1000'],
        ], [
            'descricao.required' => 'O campo descricao é obrigatório.',
            'descricao.max'      => 'A descrição pode ter no máximo 1000 caracteres.',
        ]);

        $usuario = JWTAuth::parseToken()->authenticate();
        LogService::registrar($usuario, $request->descricao);

        return response()->json(['mensagem' => 'Log registrado com sucesso.'], 201);
    }
}
