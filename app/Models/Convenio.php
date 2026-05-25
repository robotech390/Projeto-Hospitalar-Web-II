<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $table = 'convenio';

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
    ];

    public function planos()
    {
        return $this->hasMany(Plano::class, 'id_convenio');
    }
}