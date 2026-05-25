<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'plano';

    protected $fillable = [
        'descricao',
        'id_tipo_cobranca',
        'id_convenio',
    ];

    public function convenio()
    {
        return $this->belongsTo(Convenio::class, 'id_convenio');
    }

    public function tipoCobranca()
    {
        return $this->belongsTo(TipoCobranca::class, 'id_tipo_cobranca');
    }
}