<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receita extends Model
{
    protected $table = 'receita';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['observacoes', 'farmacia', 'data_emissao', 'id_consulta', 'data_criacao', 'data_alteracao'];

    protected $casts = ['data_emissao' => 'date', 'data_criacao' => 'datetime', 'data_alteracao' => 'datetime'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }

    public function medicamentos()
    {
        return $this->hasMany(MedicamentoReceita::class, 'id_receita');
    }
}
