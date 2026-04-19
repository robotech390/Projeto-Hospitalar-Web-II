<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="SolicitacaoExame",
 *     title="SolicitacaoExame",
 *     description="Modelo de Solicitação de Exame",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="data", type="string", format="date-time", example="2026-04-18 14:30:00"),
 *     @OA\Property(property="justificativa", type="string", example="Dores de cabeça constantes"),
 *     @OA\Property(property="prioridade", type="integer", example=1),
 *     @OA\Property(property="id_consulta", type="integer", example=10),
 *     @OA\Property(property="status", type="string", example="Pendente"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SolicitacaoExame extends Model
{
    protected $table = 'solicitacao_exame';

    protected $fillable = [
        'data',
        'justificativa',
        'prioridade',
        'id_consulta',
        'status',
    ];

    // Status Constants
    const STATUS_PENDENTE = 'Pendente';
    const STATUS_EM_ANDAMENTO = 'Em Andamento';
    const STATUS_COLETADO = 'Coletado';
    const STATUS_EM_ANALISE = 'Em Análise';
    const STATUS_CONCLUIDO = 'Concluído';

    /**
     * Get the items for the solicitation.
     */
    public function itensExame(): HasMany
    {
        return $this->hasMany(ItemExame::class, 'id_solicitacao');
    }

    /**
     * Interact with the solicitation's date.
     */
    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            set: fn (string $value) => Carbon::parse($value),
        );
    }
}
