<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoConsultaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_consulta')->insert([
            [
                'descricao' => 'Consulta Geral',
                'valor'     => 150.00,
            ],
            [
                'descricao' => 'Retorno',
                'valor'     => 80.00,
            ],
            [
                'descricao' => 'Urgência',
                'valor'     => 250.00,
            ],
        ]);
    }
}
