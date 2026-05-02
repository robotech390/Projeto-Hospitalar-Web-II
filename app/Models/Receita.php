<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receita extends Model
{
    use SoftDeletes;

    protected $table = 'receita';

    protected $fillable = ['observacoes', 'farmacia', 'data_emissao', 'id_consulta'];

    protected $casts = ['data_emissao' => 'date'];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }

    public function medicamentos()
    {
        return $this->hasMany(MedicamentoReceita::class, 'id_receita');
    }
}
