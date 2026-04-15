<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    public $timestamps = false;
    
    protected $fillable = ['id_produto', 'numero', 'data_validade', 'quantidade_produtos', 'ativo'];

    // Este relacionamento é obrigatório para puxar o nome do remédio no Dashboard
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_produto');
    }
}