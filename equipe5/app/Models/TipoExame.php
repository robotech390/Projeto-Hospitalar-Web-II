<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="TipoExame",
 *     title="TipoExame",
 *     description="Modelo de Tipo de Exame",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="Hemograma Completo"),
 *     @OA\Property(property="tipo", type="string", example="Sangue"),
 *     @OA\Property(property="preco", type="number", format="float", example=25.50),
 *     @OA\Property(property="preparo", type="string", example="Jejum de 8 horas"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class TipoExame extends Model
{
    protected $table = 'tipo_exame';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = [
        'nome',
        'tipo',
        'preco',
        'preparo',
    ];

    /**
     * Get the items for the exam type.
     */
    public function itensExame(): HasMany
    {
        return $this->hasMany(ItemExame::class, 'id_tipo_exame');
    }
}
