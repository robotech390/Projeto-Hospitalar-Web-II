<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoExame extends Model
{
    protected $table = 'tipo_exame';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = [
        'nome',
        'tipo',
        'preco',
        'preparo',
        'data_criacao',
        'data_alteracao'
    ];

    protected $casts = ['data_criacao' => 'datetime', 'data_alteracao' => 'datetime'];

    public function itensExame()
    {
        return $this->hasMany(ItensExame::class, 'id_tipo_exame');
    }
}