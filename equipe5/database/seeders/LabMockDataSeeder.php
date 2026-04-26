<?php

namespace Database\Seeders;

use App\Models\ItemExame;
use App\Models\TipoExame;
use App\Models\SolicitacaoExame;
use Illuminate\Database\Seeder;

class LabMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Gera dados de teste para Fila de Coleta e Lançamento de Resultados.
     */
    public function run(): void
    {
        // Garante que existam tipos de exame
        if (TipoExame::count() === 0) {
            $this->call(TipoExameSeeder::class);
        }

        $tipos = TipoExame::all();

        // Criar uma solicitação genérica para agrupar os itens de teste
        $solicitacao = SolicitacaoExame::create([
            'data' => now(),
            'justificativa' => 'Mock de teste para o módulo de Laboratório',
            'prioridade' => 1,
            'id_consulta' => rand(100, 999),
        ]);

        $itens = [
            [
                'id_solicitacao' => $solicitacao->id,
                'id_tipo_exame' => $tipos->where('tipo', 'Sangue')->first()?->id ?? $tipos->random()->id,
                'status' => 'Pendente', // Aparece apenas na Fila de Coleta
            ],
            [
                'id_solicitacao' => $solicitacao->id,
                'id_tipo_exame' => $tipos->where('tipo', 'Sangue')->first()?->id ?? $tipos->random()->id,
                'status' => 'Coletado', // Aparece em AMBOS (Fila de Coleta e Lançamento de Resultados)
            ],
            [
                'id_solicitacao' => $solicitacao->id,
                'id_tipo_exame' => $tipos->where('tipo', 'Imagem')->first()?->id ?? $tipos->random()->id,
                'status' => 'Em Análise', // Aparece apenas no Lançamento de Resultados
            ],
            [
                'id_solicitacao' => $solicitacao->id,
                'id_tipo_exame' => $tipos->where('tipo', 'Urina')->first()?->id ?? $tipos->random()->id,
                'status' => 'Em Análise', // Aparece apenas no Lançamento de Resultados
            ],
            [
                'id_solicitacao' => $solicitacao->id,
                'id_tipo_exame' => $tipos->random()->id,
                'status' => 'Pendente', // Aparece apenas na Fila de Coleta
            ],
        ];

        foreach ($itens as $itemData) {
            ItemExame::create($itemData);
        }

        $this->command->info('Dados de mock para Laboratório criados com sucesso!');
    }
}
