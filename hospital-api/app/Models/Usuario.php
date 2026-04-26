<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Model Usuario — autenticação JWT.
 * Vinculado a uma Pessoa via id_pessoa.
 */
class Usuario extends Authenticatable implements JWTSubject
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'usuario', 'email', 'senha', 'funcao',
        'id_pessoa', 'id_cadastro', 'primeiro_acesso',
    ];

    protected $hidden = ['senha'];

    protected $casts = ['primeiro_acesso' => 'boolean'];

    public function getJWTIdentifier(): mixed { return $this->getKey(); }

    public function getJWTCustomClaims(): array
    {
        return [
            'funcao'          => $this->funcao,
            'id_cadastro'     => $this->id_cadastro,
            'primeiro_acesso' => $this->primeiro_acesso,
        ];
    }

    public function getAuthPassword(): string { return $this->senha; }

    public function logs(): HasMany
    {
        return $this->hasMany(HistoricoLog::class, 'id_usuario');
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($u) => $u->data_criacao   = now());
        static::updating(fn($u) => $u->data_alteracao = now());
    }
}
