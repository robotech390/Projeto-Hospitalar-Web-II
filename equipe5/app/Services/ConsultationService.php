<?php

namespace App\Services;

class ConsultationService
{
    protected $patientService;
    protected $doctorService;

    public function __construct(PatientService $patientService, DoctorService $doctorService)
    {
        $this->patientService = $patientService;
        $this->doctorService = $doctorService;
    }

    /**
     * Busca dados da consulta, incluindo paciente e médico.
     * Atualmente mockado, futuramente via microserviço.
     */
    public function getConsultationData(int $id): ?array
    {
        return [
            'id' => $id,
            'paciente' => $this->patientService->getPatientData($id),
            'medico' => $this->doctorService->getDoctorData($id),
            'data_consulta' => date('Y-m-d H:i:s'),
            'status' => 'Concluída',
        ];
    }

    /**
     * Retorna todas as consultas (mockado).
     */
    public function getAllConsultations(): array
    {
        $consultations = [];
        for ($i = 1; $i <= 10; $i++) {
            $consultations[] = $this->getConsultationData($i);
        }
        return $consultations;
    }
}
