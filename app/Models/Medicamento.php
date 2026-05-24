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
/*
medicamento_receita:
id
id_receita
id_medicamento

medicamento:
id
nome
dosagem
principio_ativo
id_tipo_medicamento
preco

tipo_medicamento:
id
descricao

lote:
id
id_medicamento
numero
data_validade
quantidade_produtos
ativo