<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoExame extends Model
{
    use SoftDeletes;

    protected $table = 'solicitacao_exame';

    protected $fillable = ['data', 'justificativa', 'prioridade', 'id_consulta'];

    protected $casts = ['data' => 'datetime'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }

    public function itens()
    {
        return $this->hasMany(ItensExame::class, 'id_solicitacao');
    }
}
