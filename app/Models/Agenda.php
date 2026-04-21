<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_medico',
        'data_disponibilidade',
        'hora_inicio',
        'hora_fim',
        'plantao',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico');
    }
}
