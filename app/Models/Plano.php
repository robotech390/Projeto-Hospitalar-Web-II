<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'plano';
    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'id_tipo_cobranca',
        'id_convenio',
    ];

    public function convenio()
    {
        return $this->belongsTo(Convenio::class, 'id_convenio');
    }

    public function tipoCobranca()
    {
        return $this->belongsTo(TipoCobranca::class, 'id_tipo_cobranca');
    }

    public function coberturasConsulta()
    {
        return $this->hasMany(PlanoCoberturaConsulta::class, 'id_plano');
    }

    public function coberturasExame()
    {
        return $this->hasMany(PlanoCoberturaExame::class, 'id_plano');
    }

    public function coberturasMedicamento()
    {
        return $this->hasMany(PlanoCoberturaMedicamento::class, 'id_plano');
    }
    
    public function cobre(string $origem, int $id_tipo): bool
    {
        switch ($origem) {
            case 'consulta':
                return $this->coberturasConsulta()->where('id_tipo_consulta', $id_tipo)->exists();
            case 'exame':
                return $this->coberturasExame()->where('id_tipo_exame', $id_tipo)->exists();
            case 'medicamento':
                return $this->coberturasMedicamento()->where('id_tipo_medicamento', $id_tipo)->exists();
            default:
                return false;
        }
    }
}