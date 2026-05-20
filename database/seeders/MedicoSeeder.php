<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insere as pessoas dos médicos
        $idPessoa1 = DB::table('pessoa')->insertGetId([
            'nome'            => 'Dr. Fernando Augusto Melo',
            'cpf'             => '678.901.234-55',
            'data_nascimento' => '1972-04-10',
            'email'           => 'fernando.melo@hospital.com',
            'telefone'        => '(11) 99100-1111',
            'id_endereco'     => null,
        ]);

        $idPessoa2 = DB::table('pessoa')->insertGetId([
            'nome'            => 'Dra. Beatriz Helena Ramos',
            'cpf'             => '789.012.345-66',
            'data_nascimento' => '1980-08-25',
            'email'           => 'beatriz.ramos@hospital.com',
            'telefone'        => '(11) 99200-2222',
            'id_endereco'     => null,
        ]);

        $idPessoa3 = DB::table('pessoa')->insertGetId([
            'nome'            => 'Dr. Gustavo Henrique Barbosa',
            'cpf'             => '890.123.456-77',
            'data_nascimento' => '1968-12-03',
            'email'           => 'gustavo.barbosa@hospital.com',
            'telefone'        => '(11) 99300-3333',
            'id_endereco'     => null,
        ]);

        // Insere os médicos
        DB::table('medico')->insert([
            [
                'id_pessoa'        => $idPessoa1,
                'especialidade'    => 'Cardiologia',
                'sub_especialidade' => 'Eletrofisiologia',
                'crm'              => 'CRM-SP 123456',
                'uf_crm'           => 'SP',
                'tipo'             => 'Especialista',
                'status'           => 'ativo',
            ],
            [
                'id_pessoa'        => $idPessoa2,
                'especialidade'    => 'Clínica Geral',
                'sub_especialidade' => null,
                'crm'              => 'CRM-SP 234567',
                'uf_crm'           => 'SP',
                'tipo'             => 'Geral',
                'status'           => 'ativo',
            ],
            [
                'id_pessoa'        => $idPessoa3,
                'especialidade'    => 'Ortopedia',
                'sub_especialidade' => 'Coluna Vertebral',
                'crm'              => 'CRM-SP 345678',
                'uf_crm'           => 'SP',
                'tipo'             => 'Especialista',
                'status'           => 'ativo',
            ],
        ]);
    }
}
