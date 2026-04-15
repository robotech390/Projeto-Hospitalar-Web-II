<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMedicamento extends Model
{
    protected $table = 'tipo_medicamento';
    public $timestamps = false;
    protected $fillable = ['descricao'];
}