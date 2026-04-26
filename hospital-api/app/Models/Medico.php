<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Medico.
 * Um médico é uma Pessoa com CRM. Consumido pelo Grupo 2 para agendamentos.
 *
 * @property int    $id
 * @property int    $id_pessoa
 * @property string $tipo          Ex: Clínico Geral
 * @property string $crm
 * @property string $uf_crm
 * @property string $especialidade
 * @property string $status        A | I
 */
class Medico extends Model
{
    protected $table      = 'medico';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_pessoa', 'tipo', 'crm', 'uf_crm',
        'especialidade', 'sub_especialidade', 'data_contratacao', 'status',
    ];

    protected $casts = ['data_contratacao' => 'date'];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa');
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(Agenda::class, 'id_medico');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->data_criacao   = now());
        static::updating(fn($m) => $m->data_alteracao = now());
    }
}
