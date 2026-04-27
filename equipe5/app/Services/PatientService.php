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
        $pacientes = [
            1 => ['nome' => 'Maria Silva', 'cpf' => '111.111.111-11', 'data_nascimento' => '1985-05-20'],
            2 => ['nome' => 'João Souza', 'cpf' => '222.222.222-22', 'data_nascimento' => '1990-10-15'],
            3 => ['nome' => 'Carlos Lima', 'cpf' => '333.333.333-33', 'data_nascimento' => '1978-03-30'],
            4 => ['nome' => 'Ana Paula', 'cpf' => '444.444.444-44', 'data_nascimento' => '1995-12-12'],
            5 => ['nome' => 'Pedro Santos', 'cpf' => '555.555.555-55', 'data_nascimento' => '1982-07-07'],
        ];

        // Retorna um paciente baseado no ID (ciclando entre os mocks)
        return $pacientes[($id % 5) + 1];
    }
}
