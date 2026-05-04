<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnostico';

    protected $fillable = ['cid', 'descricao', 'id_consulta'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }
}
