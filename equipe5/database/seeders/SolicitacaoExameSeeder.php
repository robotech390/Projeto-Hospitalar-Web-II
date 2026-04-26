<?php

namespace Database\Seeders;

use App\Models\SolicitacaoExame;
use App\Models\TipoExame;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SolicitacaoExameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposExame = TipoExame::all();

        if ($tiposExame->isEmpty()) {
            $this->command->warn('Nenhum TipoExame encontrado. Por favor, execute o TipoExameSeeder primeiro.');
            return;
        }

        $solicitacoes = [
            [
                'data' => now()->subDays(2),
                'justificativa' => 'Paciente apresenta sintomas de anemia e cansaço excessivo.',
                'prioridade' => 1,
                'id_consulta' => 101, // Exemplo de ID vindo de outra API
            ],
            [
                'data' => now()->subDay(),
                'justificativa' => 'Check-up anual de rotina.',
                'prioridade' => 2,
                'id_consulta' => 102,
            ],
            [
                'data' => now(),
                'justificativa' => 'Suspeita de infecção urinária.',
                'prioridade' => 1,
                'id_consulta' => 103,
            ],
        ];

        foreach ($solicitacoes as $data) {
            $solicitacao = SolicitacaoExame::create($data);

            // Vincular de 1 a 3 tipos de exames aleatórios para cada solicitação
            $tiposParaVincular = $tiposExame->random(rand(1, 3));

            foreach ($tiposParaVincular as $tipo) {
                // Criando o ItemExame (pode ser via attach ou create no modelo ItemExame)
                $solicitacao->tiposExame()->attach($tipo->id, [
                    'status' => 'Pendente', // status existe na itens_exame, não na solicitacao_exame
                    'data_criacao' => now(),
                    'data_alteracao' => now(),
                ]);
            }
        }
    }
}
