<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consulta extends Model
{
    use SoftDeletes;

    protected $table = 'consulta';

    protected $fillable = [
        'descricao',
        'data',
        'hora_inicio',
        'hora_fim',
        'data_check_in',
        'status',
        'id_tipo_consulta',
        'id_paciente',
        'id_medico',
    ];

    protected $casts = [
        'data'          => 'date',
        'hora_inicio'   => 'datetime',
        'hora_fim'      => 'datetime',
        'data_check_in' => 'datetime',
    ];

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'id_consulta');
    }

    public function receitas()
    {
        return $this->hasMany(Receita::class, 'id_consulta');
    }

    public function solicitacoesExame()
    {
        return $this->hasMany(SolicitacaoExame::class, 'id_consulta');
    }
}
