<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoCoberturaConsulta extends Model
{
    protected $table = 'plano_cobertura_consulta';

    protected $fillable = [
        'id_plano',
        'id_tipo_consulta',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }

    public function tipoConsulta()
    {
        //return $this->belongsTo(TipoConsulta::class, 'id_tipo_consulta');
        return 1;
    }
}