<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnostico';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['cid', 'descricao', 'id_consulta', 'data_criacao', 'data_alteracao'];

    protected $casts = [
        'data_criacao'  => 'datetime',
        'data_alteracao'=> 'datetime',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }
}
