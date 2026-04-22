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
     * Sobrescrevemos o store porque a lógica é complexa (Transações + Pivot)
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
