<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    // Força o nome exato da sua tabela
    protected $table = 'medicamento';
    
    // Desativa a busca por created_at e updated_at
    public $timestamps = false;

    // Libera as colunas exatas da sua modelagem para inserção
    protected $fillable = [
        'nome', 
        'dosagem', 
        'principio_ativo', 
        'id_tipo_medicamento', 
        'preco'
    ];
}