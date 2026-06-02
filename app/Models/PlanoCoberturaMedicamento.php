<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoCoberturaMedicamento extends Model
{
    protected $table = 'plano_cobertura_medicamento';

    protected $fillable = [
        'id_plano',
        'id_tipo_medicamento',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }

    public function tipoMedicamento()
    {
        // return $this->belongsTo(Medicamento::class, 'id_tipo_medicamento');
        return 1;
    }
}