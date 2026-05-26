<?php

namespace App\Http\Requests;

/**
 * Validação da redefinição de senha via token enviado por e-mail.
 *
 * Regras de senha:
 *  - Mínimo 8 caracteres
 *  - Deve conter ao menos uma letra e um número
 *  - Deve ser confirmada
 */
class RedefinirSenhaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email'                   => ['required', 'email'],
            'token'                   => ['required', 'string', 'size:64'],
            'nova_senha'              => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'nova_senha_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                   => 'Informe o e-mail.',
            'email.email'                      => 'Informe um e-mail válido.',
            'token.required'                   => 'Token de redefinição não informado.',
            'token.size'                       => 'Token de redefinição inválido.',
            'nova_senha.required'              => 'Informe a nova senha.',
            'nova_senha.min'                   => 'A nova senha deve ter no mínimo 8 caracteres.',
            'nova_senha.confirmed'             => 'As senhas não conferem.',
            'nova_senha.regex'                 => 'A senha deve conter ao menos uma letra e um número.',
            'nova_senha_confirmation.required' => 'Confirme a nova senha.',
        ];
    }
}
