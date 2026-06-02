<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoCoberturaExame extends Model
{
    protected $table = 'plano_cobertura_exame';

    protected $fillable = [
        'id_plano',
        'id_tipo_exame',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }

    public function tipoExame()
    {
        // return $this->belongsTo(Exame::class, 'id_tipo_exame');
        return 1;
    }
}