<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model HistoricoLog — tabela usuario_log no banco.
 * Registros imutáveis de ações. Todos os grupos enviam logs via POST /api/logs.
 */
class HistoricoLog extends Model
{
    protected $table      = 'usuario_log';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = ['id_usuario', 'log', 'data'];

    protected $casts = ['data' => 'datetime'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
