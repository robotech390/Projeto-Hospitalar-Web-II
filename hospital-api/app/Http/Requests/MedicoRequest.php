<?php

namespace App\Http\Requests;

class MedicoRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nome'              => ['required', 'string', 'max:100'],
            'cpf'               => ['required', 'string', 'size:11'],
            'data_nascimento'   => ['nullable', 'date'],
            'email'             => ['required', 'email', 'max:100'],
            'telefone'          => ['nullable', 'string', 'max:11'],
            'crm'               => ['required', 'string', 'max:6'],
            'uf_crm'            => ['required', 'string', 'size:2'],
            'tipo'              => ['nullable', 'string', 'max:50'],
            'especialidade'     => ['nullable', 'string', 'max:100'],
            'sub_especialidade' => ['nullable', 'string', 'max:100'],
            'data_contratacao'  => ['nullable', 'date'],
            'status'            => ['nullable', 'in:A,I'],
            'endereco'               => ['nullable', 'array'],
            'endereco.cep'           => ['nullable', 'string', 'size:8'],
            'endereco.logradouro'    => ['nullable', 'string', 'max:100'],
            'endereco.numero'        => ['nullable', 'string', 'max:10'],
            'endereco.complemento'   => ['nullable', 'string', 'max:100'],
            'endereco.cidade'        => ['nullable', 'string', 'max:100'],
            'endereco.estado'        => ['nullable', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'   => 'O nome é obrigatório.',
            'cpf.required'    => 'O CPF é obrigatório.',
            'cpf.size'        => 'O CPF deve ter 11 dígitos sem formatação.',
            'email.required'  => 'O e-mail é obrigatório.',
            'crm.required'    => 'O CRM é obrigatório.',
            'uf_crm.required' => 'A UF do CRM é obrigatória.',
            'uf_crm.size'     => 'A UF deve ter 2 caracteres. Ex: SC',
            'status.in'       => 'Status deve ser A (ativo) ou I (inativo).',
        ];
    }
}
