<?php

namespace App\Http\Requests;

/**
 * Validação para troca de senha no primeiro acesso.
 */
class AlterarSenhaPrimeiroAcessoRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email'          => ['required', 'email'],
            'senha_atual'    => ['required', 'string'],
            'nova_senha'     => ['required', 'string', 'min:8', 'confirmed'],
            'nova_senha_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                      => 'O campo e-mail é obrigatório.',
            'senha_atual.required'                => 'Informe a senha de primeiro acesso recebida por e-mail.',
            'nova_senha.required'                 => 'A nova senha é obrigatória.',
            'nova_senha.min'                      => 'A nova senha deve ter no mínimo 8 caracteres.',
            'nova_senha.confirmed'                => 'A confirmação da nova senha não confere.',
            'nova_senha_confirmation.required'    => 'A confirmação da nova senha é obrigatória.',
        ];
    }
}
