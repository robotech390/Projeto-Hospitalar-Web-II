<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemContaHospitalar extends Model
{
    protected $table = 'item_conta_hospitalar';

    protected $fillable = [
        'id_conta_hospitalar',
        'origem',
        'id_origem',
        'descricao',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'coberto_convenio', //booleano
    ];

    protected $casts = [
        'coberto_convenio' => 'boolean',
    ];

    public function contaHospitalar()
    {
        return $this->belongsTo(ContaHospitalar::class, 'id_conta_hospitalar');
    }

    public function origem()
    {
        switch ($this->origem) {
            case 'consulta':
                return $this->belongsTo(Consulta::class, 'id_origem');
            case 'exame':
                return $this->belongsTo(MedicamentoReceita::class, 'id_origem');
            case 'procedimento':
                return $this->belongsTo(SolicitacaoExame::class, 'id_origem');
            default:
                return null;
        }
    }
}