<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCobranca extends Model
{
    /** @use HasFactory<\Database\Factories\TipoCobrancaFactory> */
    use HasFactory;
    protected $primaryKey = 'id'; 
    protected $table = 'tipo_cobranca';
    public $timestamps = false;
    protected $fillable = ['descricao', 'data_criacao', 'data_alteracao'];

}
