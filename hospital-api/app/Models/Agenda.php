<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model de Agenda (Plantões dos Médicos).
 *
 * Representa um bloco de disponibilidade de um médico em uma data específica.
 * O Grupo 2 consome GET /api/medicos/{id}/agenda ou GET /api/agenda
 * para saber os horários disponíveis de cada médico.
 *
 * @property int    $id
 * @property int    $id_medico
 * @property string $data_disponibilidade  Data do plantão (YYYY-MM-DD)
 * @property string $hora_inicio           Horário de início (HH:MM:SS)
 * @property string $hora_fim              Horário de término (HH:MM:SS)
 * @property int    $plantao               0 = turno normal | 1 = plantão
 */
class Agenda extends Model
{
    protected $table      = 'agenda';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_medico',
        'data_disponibilidade',
        'hora_inicio',
        'hora_fim',
        'plantao',
    ];

    protected $casts = [
        'data_disponibilidade' => 'date',
        'plantao'              => 'boolean',
    ];

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico');
    }

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Agenda $agenda) {
            $agenda->data_criacao = now();
        });

        static::updating(function (Agenda $agenda) {
            $agenda->data_alteracao = now();
        });
    }
}
