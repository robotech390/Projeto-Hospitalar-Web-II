<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaFiscal extends Model
{
    protected $table = 'nota_fiscal';
    
    // Mapeia os nomes das colunas das suas imagens
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['numero', 'cpf_cnpj', 'destinatario', 'data', 'tipo'];
}