<?php

namespace App\Http\Requests;

class EsqueciSenhaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:usuarios,email']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'email.exists'   => 'Este e-mail não está cadastrado.'
        ];
    }
}