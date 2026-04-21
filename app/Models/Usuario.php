<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pessoa',
        'usuario',
        'email',
        'funcao',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_paciente');
    }
}
