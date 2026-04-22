<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    public $timestamps = false;
    
    // Atualize o fillable para id_medicamento
    protected $fillable = ['id_medicamento', 'numero', 'data_validade', 'quantidade_produtos', 'ativo'];

    public function medicamento()
    {
        // O segundo parâmetro DEVE ser id_medicamento
        return $this->belongsTo(Medicamento::class, 'id_medicamento');
    }
}