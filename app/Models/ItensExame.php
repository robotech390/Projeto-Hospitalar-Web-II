<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensExame extends Model
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

    protected $casts = ['data_resultado' => 'date'];

    public function solicitacao()
    {
        return $this->belongsTo(SolicitacaoExame::class, 'id_solicitacao');
    }
}
