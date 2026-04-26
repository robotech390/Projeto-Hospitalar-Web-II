<?php

namespace App\Http\Requests;

/**
 * Validação dos dados de criação/edição de um slot de agenda (plantão).
 */
class AgendaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id_medico'            => ['required', 'integer', 'exists:medico,id'],
            'data_disponibilidade' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'          => ['required', 'date_format:H:i'],
            'hora_fim'             => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'plantao'              => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_medico.required'            => 'O campo id_medico é obrigatório.',
            'id_medico.exists'              => 'Médico não encontrado.',
            'data_disponibilidade.required' => 'A data de disponibilidade é obrigatória.',
            'data_disponibilidade.date'     => 'Informe uma data válida.',
            'data_disponibilidade.after_or_equal' => 'A data não pode ser no passado.',
            'hora_inicio.required'          => 'O horário de início é obrigatório.',
            'hora_inicio.date_format'       => 'O horário de início deve estar no formato HH:MM. Ex: 08:00',
            'hora_fim.required'             => 'O horário de término é obrigatório.',
            'hora_fim.date_format'          => 'O horário de término deve estar no formato HH:MM. Ex: 18:00',
            'hora_fim.after'                => 'O horário de término deve ser posterior ao horário de início.',
        ];
    }
}
