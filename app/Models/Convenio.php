<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="Minha API Laravel",
 *     version="1.0.0",
 *     description="Documentação da API",
 *     @OA\Contact(email="admin@exemplo.com")
 * )
 */
#[OA\Schema(
    schema: "Usuario",
    title: "Usuario",
    description: "Schema do modelo Usuário",
    required: ["id", "nome", "email"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nome", type: "string", example: "João Silva"),
        new OA\Property(property: "email", type: "string", format: "email", example: "joao@exemplo.com")
    ]
)]
class Convenio extends Model
{
    protected $table = 'convenio';

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
    ];

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id_convenio');
    }
}