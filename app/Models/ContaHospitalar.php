<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaHospitalar extends Model
{
    protected $table = 'conta_hospitalar';

    protected $fillable = [
        'id_consulta',
        'id_paciente',
        'id_plano',
        'status',
        'valor_total',
        'valor_convenio',
        'valor_paciente',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta');
    }
    
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }

    public function itens()
    {
        return $this->hasMany(ItemContaHospitalar::class, 'id_conta_hospitalar');
    }

    public function fatura()
    {
        return $this->hasOne(Fatura::class, 'id_conta_hospitalar');
    }
}