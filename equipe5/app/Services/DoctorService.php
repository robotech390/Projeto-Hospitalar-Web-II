<?php

namespace App\Services;

class DoctorService
{
    /**
     * Busca dados do médico.
     * Atualmente mockado, futuramente via microserviço.
     */
    public function getDoctorData(int $id): ?array
    {
        $medicos = [
            1 => ['nome' => 'Dr. João', 'crm' => 'CRM/SC 123456', 'especialidade' => 'Clínico Geral'],
            2 => ['nome' => 'Dra. Ana', 'crm' => 'CRM/SC 234567', 'especialidade' => 'Cardiologista'],
            3 => ['nome' => 'Dr. Pedro', 'crm' => 'CRM/SC 345678', 'especialidade' => 'Neurologista'],
            4 => ['nome' => 'Dra. Beatriz', 'crm' => 'CRM/SC 456789', 'especialidade' => 'Pediatra'],
            5 => ['nome' => 'Dr. Carlos', 'crm' => 'CRM/SC 567890', 'especialidade' => 'Ortopedista'],
        ];

        // Retorna um médico baseado no ID (ciclando entre os mocks)
        return $medicos[($id % 5) + 1];
    }
}
