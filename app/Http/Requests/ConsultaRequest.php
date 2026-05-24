<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConsultaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'data'             => 'nullable|date',
            'data_criacao'     => 'nullable|datetime',
            'data_alteracao'   => 'nullable|datetime',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fim'         => 'nullable|date_format:H:i|after:hora_inicio',
            'id_tipo_consulta' => 'nullable|integer',
            'id_paciente'      => 'nullable|integer',
            'id_medico'        => 'nullable|integer',
            'descricao'        => 'nullable|string|max:1000',
            'status'           => 'nullable|in:agendada,em_espera,em_andamento,concluida,cancelada',
            'data_check_in'    => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fim.after' => 'A hora de término deve ser posterior à hora de início.',
            'status.in'      => 'Status inválido. Use: agendada, em_espera, em_andamento, concluida ou cancelada.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
