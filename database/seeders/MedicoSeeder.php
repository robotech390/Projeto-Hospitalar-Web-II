<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //id, id_pessoa, tipo, CRM
        $medicos = [
            [
                'id_pessoa' => 1,
                'tipo' => 'Cardiologista',
                'CRM' => '123456',
            ],
            [
                'id_pessoa' => 2,
                'tipo' => 'Dermatologista',
                'CRM' => '654321',
            ],
        ];
        foreach ($medicos as $key => $value) {
            \App\Models\Medico::firstOrCreate(['CRM' => $value['CRM']], $value);
        }
    }
}
