<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitacaoExameRequest;
use App\Http\Resources\SolicitacaoExameResource;
use App\Models\SolicitacaoExame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitacaoExameController extends Controller
{
    protected string $model = SolicitacaoExame::class;
    protected string $resource = SolicitacaoExameResource::class;
    protected array $load = ['tiposExame'];

    /**
     * @OA\Get(
     *     path="/api/solicitacoes",
     *     summary="Listar todas as solicitações de exame",
     *     tags={"SolicitacaoExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitações"
     *     )
     * )
     */
    public function index()
    {
        return parent::index();
    }

    /**
     * @OA\Get(
     *     path="/api/solicitacoes/{id}",
     *     summary="Mostrar detalhes de uma solicitação",
     *     tags={"SolicitacaoExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da solicitação"
     *     )
     * )
     */
    public function show($id)
    {
        return parent::show($id);
    }

    /**
     * @OA\Post(
     *     path="/api/solicitacoes",
     *     summary="Criar uma nova solicitação de exame",
     *     tags={"SolicitacaoExame"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", format="date-time"),
     *             @OA\Property(property="justificativa", type="string"),
     *             @OA\Property(property="prioridade", type="integer"),
     *             @OA\Property(property="id_consulta", type="integer"),
     *             @OA\Property(property="tipos_exame", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitação criada"
     *     )
     * )
     */

    public function store(StoreSolicitacaoExameRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $solicitacao = SolicitacaoExame::create([
                'data' => $request->data,
                'justificativa' => $request->justificativa,
                'prioridade' => $request->prioridade,
                'id_consulta' => $request->id_consulta,
                'status' => SolicitacaoExame::STATUS_PENDENTE,
            ]);

            $solicitacao->tiposExame()->attach($request->tipos_exame, [
                'status' => SolicitacaoExame::STATUS_PENDENTE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $solicitacao->load('tiposExame');

            return new SolicitacaoExameResource($solicitacao);
        });
    }
}
