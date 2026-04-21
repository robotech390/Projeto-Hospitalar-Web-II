<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consulta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'id_tipo_consulta',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'descricao',
    ];

    public function paciente()
    {
        return $this->belongsTo(Usuario::class, 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico');
    }

    public function tipoConsulta()
    {
        return $this->belongsTo(TipoConsulta::class, 'id_tipo_consulta');
    }
}
