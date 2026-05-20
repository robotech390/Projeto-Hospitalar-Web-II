<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pessoa')->insert([
            [
                'nome'            => 'Ana Paula Ferreira',
                'cpf'             => '123.456.789-00',
                'data_nascimento' => '1985-03-15',
                'email'           => 'ana.ferreira@email.com',
                'telefone'        => '(11) 91234-5678',
                'id_endereco'     => null,
            ],
            [
                'nome'            => 'Carlos Eduardo Souza',
                'cpf'             => '234.567.890-11',
                'data_nascimento' => '1990-07-22',
                'email'           => 'carlos.souza@email.com',
                'telefone'        => '(21) 92345-6789',
                'id_endereco'     => null,
            ],
            [
                'nome'            => 'Mariana Costa Lima',
                'cpf'             => '345.678.901-22',
                'data_nascimento' => '1978-11-05',
                'email'           => 'mariana.lima@email.com',
                'telefone'        => '(31) 93456-7890',
                'id_endereco'     => null,
            ],
            [
                'nome'            => 'Roberto Alves Pereira',
                'cpf'             => '456.789.012-33',
                'data_nascimento' => '1965-01-30',
                'email'           => 'roberto.pereira@email.com',
                'telefone'        => '(41) 94567-8901',
                'id_endereco'     => null,
            ],
            [
                'nome'            => 'Juliana Nascimento Santos',
                'cpf'             => '567.890.123-44',
                'data_nascimento' => '2000-09-18',
                'email'           => 'juliana.santos@email.com',
                'telefone'        => '(51) 95678-9012',
                'id_endereco'     => null,
            ],
        ]);
    }
}
