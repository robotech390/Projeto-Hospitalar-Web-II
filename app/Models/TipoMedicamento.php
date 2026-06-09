<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class TipoMedicamento extends Model
{
    protected $table = 'tipo_medicamento';

    protected $fillable = ['descricao'];

    public function medicamentos()
    {
        return $this->hasMany(Medicamento::class, 'id_tipo_medicamento');
    }
}