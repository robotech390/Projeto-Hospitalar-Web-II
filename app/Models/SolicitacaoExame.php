<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoExame extends Model
{
    protected $table = 'solicitacao_exame';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['data', 'justificativa', 'prioridade', 'id_consulta', 'data_criacao', 'data_alteracao'];

    protected $casts = ['data' => 'datetime', 'data_criacao' => 'datetime', 'data_alteracao' => 'datetime'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }

    public function itens()
    {
        return $this->hasMany(ItensExame::class, 'id_solicitacao');
    }
}
