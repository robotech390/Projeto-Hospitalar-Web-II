<?php

namespace App\Http\Requests;

/**
 * Validação do pedido de redefinição de senha (esqueci minha senha).
 */
class EsqueciSenhaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:345'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Informe o seu e-mail.',
            'email.email'    => 'Informe um e-mail válido.',
        ];
    }
}
