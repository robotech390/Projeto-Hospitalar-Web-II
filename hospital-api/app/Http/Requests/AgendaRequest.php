<?php

namespace App\Http\Requests;

/**
 * Validação de criação/edição de plantão.
 *
 * Regra: a data deve ser futura apenas no momento da criação;
 * em edição, plantões antigos podem ser ajustados sem que isso falhe.
 */
class AgendaRequest extends BaseRequest
{
    public function rules(): array
    {
        $ehCriacao = $this->isMethod('post');

        return [
            'id_medico'            => ['required', 'integer', 'exists:medico,id'],
            'data_disponibilidade' => array_filter([
                'required',
                'date',
                $ehCriacao ? 'after_or_equal:today' : null,
            ]),
            'hora_inicio'          => ['required', 'date_format:H:i'],
            'hora_fim'             => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'plantao'              => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_medico.required'            => 'Selecione um médico.',
            'id_medico.exists'              => 'Médico não encontrado.',
            'data_disponibilidade.required' => 'Informe a data do plantão.',
            'data_disponibilidade.date'     => 'Data inválida.',
            'data_disponibilidade.after_or_equal' => 'A data do plantão não pode estar no passado.',
            'hora_inicio.required'          => 'Informe o horário de início.',
            'hora_inicio.date_format'       => 'Horário de início inválido. Use HH:MM.',
            'hora_fim.required'             => 'Informe o horário de término.',
            'hora_fim.date_format'          => 'Horário de término inválido. Use HH:MM.',
            'hora_fim.after'                => 'O horário de término deve ser depois do início.',
        ];
    }
}
