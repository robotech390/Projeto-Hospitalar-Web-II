<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="ItemExame",
 *     title="ItemExame",
 *     description="Modelo de Item de Exame",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="id_solicitacao", type="integer", example=1),
 *     @OA\Property(property="id_tipo_exame", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="Em Andamento"),
 *     @OA\Property(property="laudo", type="string", example="Resultados normais", nullable=true),
 *     @OA\Property(property="arquivo", type="string", example="path/to/file.pdf", nullable=true),
 *     @OA\Property(property="data_resultado", type="string", format="date-time", example="2026-04-19 10:00:00", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ItemExame extends Model
{
    protected $table = 'itens_exame';

    protected $fillable = [
        'id_solicitacao',
        'id_tipo_exame',
        'status',
        'laudo',
        'arquivo',
        'data_resultado',
    ];

    /**
     * Get the solicitation that owns the item.
     */
    public function solicitacaoExame(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoExame::class, 'id_solicitacao');
    }

    /**
     * Get the type of exam.
     */
    public function tipoExame(): BelongsTo
    {
        return $this->belongsTo(TipoExame::class, 'id_tipo_exame');
    }
}
