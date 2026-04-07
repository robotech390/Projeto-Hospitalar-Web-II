<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    
    public $timestamps = false;

    protected $fillable = [
        'id_produto', 
        'numero', 
        'data_validade', 
        'quantidade_produtos'
    ];

    // Opcional, mas recomendado: Relacionamento com Produto
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}