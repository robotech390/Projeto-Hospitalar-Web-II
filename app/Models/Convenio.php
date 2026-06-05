<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Convenio",
    title: "Objeto Convenio",
    description: "Modelo de dados que representa um convênio médico",
    required: ["nome", "cnpj", "telefone", "email", "id_endereco"]
)]
class Convenio extends Model
{
    /** @use HasFactory<\Database\Factories\ConvenioFactory> */
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $table = 'convenio';
    public $timestamps = false;
    protected $fillable = ['nome', 'cnpj', 'telefone', 'email', 'id_endereco', 'data_criacao', 'data_atualizacao'];
    #[OA\Property(description: "Nome do convênio", example: "Amil Saúde")]
    private string $nome;

    #[OA\Property(description: "CNPJ do convênio", example: "12.345.678/0001-99")]
    private string $cnpj;

    #[OA\Property(description: "Telefone de contato", example: "(11) 91234-5678")]
    private string $telefone;

    #[OA\Property(description: "E-mail do convênio", example: "contato@amil.com")]
    private string $email;

    #[OA\Property(description: "ID do endereço vinculado", example: 1)]
    private int $id_endereco;

    public function endereco(){
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }
}