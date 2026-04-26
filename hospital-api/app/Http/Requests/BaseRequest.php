<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Base para todos os Form Requests.
 * Garante resposta JSON padronizada em caso de falha de validação.
 */
abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'mensagem' => 'Dados inválidos.',
            'erros'    => $validator->errors(),
        ], 422));
    }
}
