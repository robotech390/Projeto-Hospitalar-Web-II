<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Validação para registrar um novo usuário no sistema.
 *
 * Regra de negócio: o campo `id_cadastro` é o ID do usuário em outro módulo
 * (ex: ID do paciente na tabela do Grupo 2). Administradores não pertencem a
 * nenhum módulo externo, portanto este campo é opcional para essa função.
 */
class RegistrarUsuarioRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nome'        => ['required', 'string', 'min:3', 'max:100'],
            'email'       => ['required', 'email', 'max:345', Rule::unique('usuario', 'email')],
            'funcao'      => ['required', 'in:administrador,medico,farmaceutico,recepcionista,paciente'],
            'id_cadastro' => [
                Rule::requiredIf(fn() => $this->input('funcao') !== 'administrador'),
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'        => 'Informe o nome completo do usuário.',
            'nome.min'             => 'O nome deve ter ao menos 3 caracteres.',
            'email.required'       => 'Informe o e-mail.',
            'email.email'          => 'Informe um e-mail válido.',
            'email.unique'         => 'Já existe um usuário com este e-mail.',
            'funcao.required'      => 'Selecione a função do usuário.',
            'funcao.in'            => 'Função inválida.',
            'id_cadastro.required' => 'O ID do cadastro é obrigatório para esta função.',
            'id_cadastro.integer'  => 'O ID do cadastro deve ser um número inteiro.',
        ];
    }

    /**
     * Força id_cadastro = null quando a função for administrador.
     * Isso permite que o frontend envie o campo em branco para admins.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('funcao') === 'administrador') {
            $this->merge(['id_cadastro' => null]);
        }
    }
}
