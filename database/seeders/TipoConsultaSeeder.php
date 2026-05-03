<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TipoConsultaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'descricao' => 'Clínica Geral',
                'valor' => 225.00
            ],
            [
                'descricao' => 'Cardiologia',
                'valor' => 325.00
            ],
            [
                'descricao' => 'Dermatologia',
                'valor' => 275.00
            ],
            [
                'descricao' => 'Ortopedia',
                'valor' => 325.00
            ],
            [
                'descricao' => 'Oftalmologia',
                'valor' => 265.00
            ],
            [
                'descricao' => 'Ginecologia',
                'valor' => 300.00
            ],
            [
                'descricao' => 'Pediatria',
                'valor' => 250.00
            ],
            [
                'descricao' => 'Psicologia/Psiquiatria',
                'valor' => 250.00
            ],
            [
                'descricao' => 'Neurologia',
                'valor' => 325.00
            ],
            [
                'descricao' => 'Gastroenterologia',
                'valor' => 325.00
            ],
            [
                'descricao' => 'Otorrinolaringologia',
                'valor' => 300.00
            ],
            [
                'descricao' => 'Fisioterapia',
                'valor' => 175.00
            ],
        ];

        foreach ($tipos as $tipo) {
            \App\Models\TipoConsulta::firstOrCreate(['descricao' => $tipo['descricao']], $tipo);
        }
    }
}