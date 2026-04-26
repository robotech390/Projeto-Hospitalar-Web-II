<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    /** @use HasFactory<\Database\Factories\ConvenioFactory> */
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $table = 'convenio';
    public $timestamps = false;
    protected $fillable = ['nome', 'cnpj', 'telefone', 'email', 'id_endereco', 'data_criacao', 'data_atualizacao'];

    public function endereco(){
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }
}
