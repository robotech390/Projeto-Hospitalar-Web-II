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
}
