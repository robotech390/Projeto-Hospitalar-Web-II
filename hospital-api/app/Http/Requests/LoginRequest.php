<?php

namespace App\Http\Requests;

/**
 * Validação dos dados de login.
 */
class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'senha' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'senha.required' => 'O campo senha é obrigatório.',
            'senha.min'      => 'A senha deve ter no mínimo 6 caracteres.',
        ];
    }
}
