<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Endereco extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $table = 'endereco';
    public $timestamps = false;
    protected $fillable = ['logradouro', 'numero', 'complemento', 'cidade', 'estado', 'cep'];
}
