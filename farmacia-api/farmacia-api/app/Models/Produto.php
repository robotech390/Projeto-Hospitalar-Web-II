<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    // Força o Laravel a buscar a tabela no singular
    protected $table = 'produto';
    
    // Impede o Laravel de procurar as colunas created_at e updated_at
    public $timestamps = false;

    // Libera os campos que o React vai enviar para serem salvos
    protected $fillable = [
        'nome', 
        'principio_ativo', 
        'dosagem', 
        'preco_unitario'
    ];
}