<?php

namespace Database\Seeders;

use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cria o usuário administrador inicial do sistema.
 * Rode com: php artisan db:seed --class=AdminSeeder
 *
 * Credenciais padrão:
 *   E-mail: admin@hospital.com
 *   Senha:  Admin@123456
 *
 * IMPORTANTE: troque a senha após o primeiro login!
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicata se rodar mais de uma vez
        if (Usuario::where('email', 'admin@hospital.com')->exists()) {
            $this->command->info('Usuário admin já existe. Nada a fazer.');
            return;
        }

        // Cria uma pessoa base para o admin
        $pessoa = Pessoa::create([
            'nome'  => 'Administrador',
            'cpf'   => '00000000000',
            'email' => 'admin@hospital.com',
        ]);

        // Cria o usuário administrador
        Usuario::create([
            'usuario'         => 'Administrador',
            'email'           => 'admin@hospital.com',
            'senha'           => Hash::make('Admin@123456'),
            'funcao'          => 'administrador',
            'id_pessoa'       => $pessoa->id,
            'id_cadastro'     => $pessoa->id,
            'primeiro_acesso' => false, // admin já começa com acesso completo
        ]);

        $this->command->info('✅ Usuário admin criado!');
        $this->command->info('   E-mail: admin@hospital.com');
        $this->command->info('   Senha:  Admin@123456');
        $this->command->warn('   ⚠ Troque a senha após o primeiro login!');
    }
}
