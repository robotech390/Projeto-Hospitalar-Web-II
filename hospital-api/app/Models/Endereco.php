<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $table      = 'endereco';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'logradouro',
        'cidade',
        'estado',
        'numero',
        'cep',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->data_criacao   = now());
        static::updating(fn($m) => $m->data_alteracao = now());
    }
}
