<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';
    
    public $timestamps = false;

    protected $fillable = [
        'nome', 
        'dosagem', 
        'principio_ativo', 
        'id_tipo_medicamento', 
        'preco'
    ];
}