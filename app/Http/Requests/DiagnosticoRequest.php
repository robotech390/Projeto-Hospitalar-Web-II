<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiagnosticoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cid'         => 'required|string|max:10',
            'descricao'   => 'required|string|max:2000',
            'id_consulta' => 'required|integer|exists:consulta,id',
        ];
    }

    public function messages(): array
    {
        return [
            'cid.required'        => 'O código CID é obrigatório.',
            'descricao.required'  => 'A descrição do diagnóstico é obrigatória.',
            'id_consulta.required'=> 'A consulta é obrigatória.',
            'id_consulta.exists'  => 'A consulta selecionada não existe.',
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
