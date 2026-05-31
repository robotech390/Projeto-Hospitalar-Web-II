<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    protected $table = 'fatura';

    protected $fillable = [
        'id_conta_hospitalar',
        'id_paciente',
        'numero_fatura',
        'status',
        'valor_total',
        'valor_convenio',
        'valor_paciente',
        'forma_pagamento',
        'data_emissao',
        'data_vencimento',
        'data_pagamento',        
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
        'data_vencimento' => 'datetime',
        'data_pagamento' => 'datetime',
    ];

    public function contaHospitalar()
    {
        return $this->belongsTo(ContaHospitalar::class, 'id_conta_hospitalar');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }
}