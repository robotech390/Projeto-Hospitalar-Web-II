<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCobranca extends Model
{
    protected $table = 'tipo_cobranca';

    protected $fillable = [
        'descricao',
    ];

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id_tipo_cobranca');
    }
}