<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicamentoReceita extends Model
{
    protected $table = 'medicamento_receita';

    protected $fillable = ['id_receita', 'id_medicamento', 'posologia', 'quantidade'];

    public function receita()
    {
        return $this->belongsTo(Receita::class, 'id_receita');
    }
}
