<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //id, nome, cpf, data_nascimento, email, telefone, id_endereco
        $pessoas = [
            [
                'nome' => 'João Silva',
                'cpf' => '123.456.789-00',
                'data_nascimento' => '1980-01-15',
                'email' => 'joao.silva@example.com',
                'telefone' => '(11) 98765-4321',
                'id_endereco' => 1,
            ],
            [
                'nome' => 'Maria Oliveira',
                'cpf' => '987.654.321-00',
                'data_nascimento' => '1990-05-20',
                'email' => 'maria.oliveira@example.com',
                'telefone' => '(11) 98765-4321',
                'id_endereco' => 2,
            ],
        ];

        foreach ($pessoas as $pessoa) {
            \App\Models\Pessoa::firstOrCreate(['cpf' => $pessoa['cpf']], $pessoa);
        }

    }
}
