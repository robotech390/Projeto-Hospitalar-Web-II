<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';

    protected $fillable = ['id_medicamento', 'numero', 'data_validade', 'quantidade_produtos', 'ativo'];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento');
    }
}