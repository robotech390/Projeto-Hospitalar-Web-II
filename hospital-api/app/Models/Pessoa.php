<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Pessoa — entidade base compartilhada entre grupos.
 * Médicos e pacientes são pessoas.
 */
class Pessoa extends Model
{
    protected $table      = 'pessoa';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nome', 'cpf', 'data_nascimento', 'email', 'telefone', 'id_endereco',
    ];

    protected $casts = ['data_nascimento' => 'date'];

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class, 'id_pessoa');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->data_criacao    = now());
        static::updating(fn($m) => $m->data_alteracao  = now());
    }
}
