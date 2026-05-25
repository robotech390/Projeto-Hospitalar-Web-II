<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';

    protected $fillable = ['nome', 'dosagem', 'principio_ativo', 'id_tipo_medicamento', 'preco'];

    public function tipoMedicamento()
    {
        return $this->belongsTo(TipoMedicamento::class, 'id_tipo_medicamento');
    }

    public function lote()
    {
        return $this->hasOne(Lote::class, 'id_medicamento');
    }
}