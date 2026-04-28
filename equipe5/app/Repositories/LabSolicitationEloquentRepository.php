<?php

namespace App\Repositories;

use App\Models\SolicitacaoExame;
use App\Models\ItemExame;
use Illuminate\Support\Facades\DB;

class LabSolicitationEloquentRepository implements LabSolicitationRepositoryInterface
{
    public function getAll(): array
    {
        return SolicitacaoExame::with(['itensExame.tipoExame'])->latest('data_criacao')->get()->toArray();
    }

    public function create(array $data): SolicitacaoExame
    {
        return DB::transaction(function () use ($data) {
            $solicitacao = SolicitacaoExame::create([
                'data' => $data['data'] ?? now(),
                'justificativa' => $data['justificativa'],
                'prioridade' => $data['prioridade'] ?? 1,
                'id_consulta' => $data['id_consulta'],
            ]);

            if (isset($data['itens']) && is_array($data['itens'])) {
                foreach ($data['itens'] as $item) {
                    ItemExame::create([
                        'id_solicitacao' => $solicitacao->id,
                        'id_tipo_exame' => $item['id_tipo_exame'],
                        'status' => 'Pendente',
                    ]);
                }
            }

            return $solicitacao;
        });
    }

    public function update(int $id, array $data): SolicitacaoExame
    {
        return DB::transaction(function () use ($id, $data) {
            $solicitacao = SolicitacaoExame::findOrFail($id);
            $solicitacao->update([
                'data' => $data['data'] ?? $solicitacao->data,
                'justificativa' => $data['justificativa'] ?? $solicitacao->justificativa,
                'prioridade' => $data['prioridade'] ?? $solicitacao->prioridade,
                'id_consulta' => $data['id_consulta'] ?? $solicitacao->id_consulta,
            ]);

            if (isset($data['itens']) && is_array($data['itens'])) {
                $requestedItemIds = collect($data['itens'])->pluck('id')->filter()->toArray();
                
                // Remove itens que não estão na requisição (Deletados no front)
                ItemExame::where('id_solicitacao', $id)
                    ->whereNotIn('id', $requestedItemIds)
                    ->delete();

                foreach ($data['itens'] as $itemData) {
                    if (isset($itemData['id']) && $itemData['id']) {
                        // Atualiza item existente
                        $item = ItemExame::where('id_solicitacao', $id)
                            ->where('id', $itemData['id'])
                            ->first();
                        
                        if ($item) {
                            $item->update([
                                'id_tipo_exame' => $itemData['id_tipo_exame'],
                                'status' => $itemData['status'] ?? $item->status,
                            ]);
                        }
                    } else {
                        // Cria novo item
                        ItemExame::create([
                            'id_solicitacao' => $solicitacao->id,
                            'id_tipo_exame' => $itemData['id_tipo_exame'],
                            'status' => 'Pendente',
                        ]);
                    }
                }
            }

            return $solicitacao;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            ItemExame::where('id_solicitacao', $id)->delete();
            return SolicitacaoExame::destroy($id) > 0;
        });
    }

    public function getById(int $id): SolicitacaoExame
    {
        return SolicitacaoExame::with(['itensExame.tipoExame'])->findOrFail($id);
    }
}
