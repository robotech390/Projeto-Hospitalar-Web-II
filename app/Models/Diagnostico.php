<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnostico extends Model
{
    use SoftDeletes;

    protected $table = 'diagnostico';

    protected $fillable = ['cid', 'descricao', 'id_consulta'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }
}
