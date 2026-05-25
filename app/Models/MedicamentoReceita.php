<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicamentoReceita extends Model
{
    protected $table = 'medicamento_receita';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['id_receita', 'id_medicamento', 'quantidade', 'posologia', 'data_criacao', 'data_alteracao'];

    protected $casts = ['data_criacao' => 'datetime', 'data_alteracao' => 'datetime'];

    public function receita()
    {
        return $this->belongsTo(Receita::class, 'id_receita');
    }
}
