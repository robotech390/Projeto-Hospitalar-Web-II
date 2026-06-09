<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensExame extends Model
{
    protected $table = 'itens_exame';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = [
        'id_solicitacao',
        'id_tipo_exame',
        'status',
        'laudo',
        'arquivo',
        'data_resultado',
        'data_criacao',
        'data_alteracao'
    ];

    protected $casts = ['data_resultado' => 'date', 'data_criacao' => 'datetime', 'data_alteracao' => 'datetime'];

    public function solicitacao()
    {
        return $this->belongsTo(SolicitacaoExame::class, 'id_solicitacao');
    }

    public function tipoExame()
    {
        return $this->belongsTo(TipoExame::class, 'id_tipo_exame');
    }
}
