<?php

namespace App\Services;

class PatientService
{
    /**
     * Busca dados do paciente.
     * Atualmente mockado, futuramente via microserviço.
     */
    public function getPatientData(int $id): ?array
    {
        // Tenta buscar o paciente diretamente do banco de dados compartilhado (tabela pessoa)
        try {
            $pessoa = \Illuminate\Support\Facades\DB::table('pessoa')->where('id', $id)->first();
            if ($pessoa) {
                return [
                    'nome' => $pessoa->nome,
                    'cpf' => $pessoa->cpf,
                    'data_nascimento' => $pessoa->data_nascimento,
                ];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao buscar paciente no banco: " . $e->getMessage());
        }

        // Fallback: Dados mockados locais caso não encontre no banco ou ocorra erro
        $pacientes = [
            1 => ['nome' => 'Maria Silva', 'cpf' => '111.111.111-11', 'data_nascimento' => '1985-05-20'],
            2 => ['nome' => 'João Souza', 'cpf' => '222.222.222-22', 'data_nascimento' => '1990-10-15'],
            3 => ['nome' => 'Carlos Lima', 'cpf' => '333.333.333-33', 'data_nascimento' => '1978-03-30'],
            4 => ['nome' => 'Ana Paula', 'cpf' => '444.444.444-44', 'data_nascimento' => '1995-12-12'],
            5 => ['nome' => 'Pedro Santos', 'cpf' => '555.555.555-55', 'data_nascimento' => '1982-07-07'],
        ];

        return $pacientes[($id % 5) + 1];
    }
}
