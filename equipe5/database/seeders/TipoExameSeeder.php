<?php

namespace Database\Seeders;

use App\Models\TipoExame;
use Illuminate\Database\Seeder;

class TipoExameSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Hemograma Completo',
                'tipo' => 'Sangue',
                'preco' => 35.00,
                'preparo' => 'Jejum de 8 a 12 horas.',
            ],
            [
                'nome' => 'Glicemia de Jejum',
                'tipo' => 'Sangue',
                'preco' => 15.00,
                'preparo' => 'Jejum de 8 horas.',
            ],
            [
                'nome' => 'Raio-X de Tórax',
                'tipo' => 'Imagem',
                'preco' => 120.00,
                'preparo' => 'Não requer preparo especial.',
            ],
            [
                'nome' => 'Ultrassonografia Abdominal',
                'tipo' => 'Imagem',
                'preco' => 200.00,
                'preparo' => 'Beber 4 copos de água 1 hora antes.',
            ],
            [
                'nome' => 'Exame de Urina Tipo 1',
                'tipo' => 'Urina',
                'preco' => 20.00,
                'preparo' => 'Coletar a primeira urina da manhã.',
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoExame::firstOrCreate(['nome' => $tipo['nome']], $tipo);
        }
    }
}
