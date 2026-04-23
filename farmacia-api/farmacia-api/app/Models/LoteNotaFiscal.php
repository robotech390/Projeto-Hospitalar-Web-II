<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteNotaFiscal extends Model
{
    protected $table = 'lote_notafiscal';
    
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_alteracao';

    protected $fillable = ['id_nota_fiscal', 'id_lote', 'icms', 'cfop', 'quantidade'];
}