<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consulta';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = [
        'descricao',
        'data',
        'data_check_in',
        'hora_inicio',
        'hora_fim',
        'status',
        'id_tipo_consulta',
        'id_paciente',
        'id_medico',
    ];

    protected $casts = [
        'data'          => 'date',
        'data_criacao'  => 'datetime',
        'data_alteracao'=> 'datetime',
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
