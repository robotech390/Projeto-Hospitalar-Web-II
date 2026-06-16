<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SolicitacaoExameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_consulta'           => 'required|integer|exists:consulta,id',
            'justificativa'         => 'nullable|string|max:1000',
            'prioridade'            => 'required|integer|in:1,2,3',
            'itens'                 => 'sometimes|array|min:1',
            'itens.*.id_tipo_exame' => 'required_with:itens|integer|exists:tipo_exame,id',
            'itens.*.status'        => 'nullable|string|max:30',
            'itens.*.arquivo'       => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'prioridade.in' => 'Prioridade inválida. Use 1 (baixa), 2 (média) ou 3 (alta).',
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
