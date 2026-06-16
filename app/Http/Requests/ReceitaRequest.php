<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReceitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_consulta'                   => 'required|integer|exists:consulta,id',
            'observacoes'                   => 'nullable|string|max:2000',
            'farmacia'                      => 'nullable|string|max:255',
            'data_emissao'                  => 'required|date',
            'medicamentos'                  => 'nullable|array',
            'medicamentos.*.id_medicamento' => 'required|integer',
            'medicamentos.*.posologia'      => 'nullable|string|max:500',
            'medicamentos.*.quantidade'     => 'nullable|integer|min:1',
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
