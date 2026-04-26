<?php

namespace App\Http\Requests;

/**
 * Validação dos dados enviados pelas outras equipes para registrar um usuário.
 *
 * As equipes devem enviar:
 *  - email      : e-mail do usuário a ser criado
 *  - funcao     : papel do usuário no sistema
 *  - id_cadastro: ID do cadastro do usuário no sistema de origem
 *  - nome       : nome completo do usuário
 */
class RegistrarUsuarioRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email'       => ['required', 'email', 'unique:usuario,email'],
            'funcao'      => ['required', 'string', 'in:administrador,medico,farmaceutico,recepcionista,paciente'],
            'id_cadastro' => ['required', 'integer', 'min:1'],
            'nome'        => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'       => 'O campo e-mail é obrigatório.',
            'email.email'          => 'Informe um e-mail válido.',
            'email.unique'         => 'Já existe um usuário com este e-mail.',
            'funcao.required'      => 'O campo funcao é obrigatório.',
            'funcao.in'            => 'A funcao deve ser: administrador, medico, farmaceutico, recepcionista ou paciente.',
            'id_cadastro.required' => 'O campo id_cadastro é obrigatório.',
            'id_cadastro.integer'  => 'O id_cadastro deve ser um número inteiro.',
            'nome.required'        => 'O campo nome é obrigatório.',
        ];
    }
}
