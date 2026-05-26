<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model TokenRedefinicaoSenha — guarda os tokens enviados por e-mail
 * para o fluxo de "esqueci minha senha". Tokens são imutáveis e expiram em 60 minutos.
 */
class TokenRedefinicaoSenha extends Model
{
    protected $table      = 'token_redefinicao_senha';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'expira_em',
        'utilizado_em',
    ];

    protected $casts = [
        'expira_em'    => 'datetime',
        'utilizado_em' => 'datetime',
    ];

    /**
     * Verifica se o token ainda pode ser utilizado.
     */
    public function ehValido(): bool
    {
        return $this->utilizado_em === null && $this->expira_em->isFuture();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($token) {
            if (!$token->data_criacao) {
                $token->data_criacao = now();
            }
        });
    }
}
