<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoExameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => ['required', 'date', 'after_or_equal:today'],
            'justificativa' => ['nullable', 'string'],
            'prioridade' => ['required', 'integer'],
            'id_consulta' => ['required', 'integer'],
            'tipos_exame' => ['required', 'array', 'min:1'],
            'tipos_exame.*' => ['integer', 'exists:tipo_exame,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.after_or_equal' => 'Não é possível cadastrar solicitações com data no passado.',
            'tipos_exame.min' => 'Uma solicitação precisa de no mínimo 1 tipo de exame.',
            'tipos_exame.*.distinct' => 'Não é permitido inserir duas vezes o mesmo tipo de exame na mesma solicitação.',
        ];
    }
}
