<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Validation\Rule;

/**
 * Validação de criação/edição de médico.
 *
 * Regras de negócio:
 *  - nome, cpf, e-mail, telefone, CRM, UF do CRM, especialidade e
 *    tipo de atendimento são obrigatórios (não faz sentido um médico
 *    sem essas informações no hospital).
 *  - data_nascimento e data_contratacao são obrigatórias para auditoria.
 *  - sub_especialidade e endereço são opcionais.
 */
class MedicoRequest extends BaseRequest
{
    public function rules(): array
    {
        $idMedico = $this->route('medico');
        $idPessoa = $this->resolverIdPessoaDoMedico($idMedico);

        return [
            'nome'              => ['required', 'string', 'min:3', 'max:100'],
            'cpf'               => ['required', new Cpf, Rule::unique('pessoa', 'cpf')->ignore($idPessoa)],
            'data_nascimento'   => ['required', 'date', 'before:today'],
            'email'             => ['required', 'email', 'max:100', Rule::unique('pessoa', 'email')->ignore($idPessoa)],
            'telefone'          => ['required', 'string', 'min:10', 'max:11'],
            'crm'               => ['required', 'string', 'max:6', Rule::unique('medico', 'crm')->ignore($idMedico)],
            'uf_crm'            => ['required', 'string', 'size:2'],
            'tipo'              => ['required', 'string', 'max:50'],
            'especialidade'     => ['required', 'string', 'max:100'],
            'sub_especialidade' => ['nullable', 'string', 'max:100'],
            'data_contratacao'  => ['required', 'date', 'before_or_equal:today'],
            'status'            => ['nullable', 'in:A,I'],

            'endereco'            => ['nullable', 'array'],
            'endereco.cep'        => ['nullable', 'string', 'size:8'],
            'endereco.logradouro' => ['nullable', 'string', 'max:100'],
            'endereco.numero'     => ['nullable', 'string', 'max:10'],
            'endereco.cidade'     => ['nullable', 'string', 'max:100'],
            'endereco.estado'     => ['nullable', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'              => 'Informe o nome completo do médico.',
            'nome.min'                   => 'O nome deve ter ao menos 3 caracteres.',
            'cpf.required'               => 'O CPF é obrigatório.',
            'cpf.unique'                 => 'Já existe um cadastro com este CPF.',
            'data_nascimento.required'   => 'Informe a data de nascimento.',
            'data_nascimento.before'     => 'A data de nascimento deve ser anterior a hoje.',
            'email.required'             => 'Informe o e-mail.',
            'email.email'                => 'Informe um e-mail válido.',
            'email.unique'               => 'Já existe um cadastro com este e-mail.',
            'telefone.required'          => 'Informe o telefone para contato.',
            'telefone.min'               => 'Telefone inválido (10 ou 11 dígitos).',
            'telefone.max'               => 'Telefone inválido (10 ou 11 dígitos).',
            'crm.required'               => 'Informe o número do CRM.',
            'crm.unique'                 => 'Já existe um médico cadastrado com este CRM.',
            'uf_crm.required'            => 'Informe a UF do CRM.',
            'uf_crm.size'                => 'A UF do CRM deve ter 2 caracteres. Ex: SC',
            'tipo.required'              => 'Informe o tipo de atendimento (ex: Clínico Geral, Especialista).',
            'especialidade.required'     => 'Informe a especialidade do médico.',
            'data_contratacao.required'  => 'Informe a data de contratação.',
            'data_contratacao.before_or_equal' => 'A data de contratação não pode ser futura.',
            'status.in'                  => 'Status deve ser A (Ativo) ou I (Inativo).',
            'endereco.cep.size'          => 'O CEP deve conter 8 dígitos.',
            'endereco.estado.size'       => 'A UF do estado deve ter 2 caracteres.',
        ];
    }

    /**
     * Remove formatação (CPF, telefone, CEP) antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(array_filter([
            'cpf'      => $this->cpf      ? preg_replace('/\D/', '', $this->cpf)      : null,
            'telefone' => $this->telefone ? preg_replace('/\D/', '', $this->telefone) : null,
        ]));

        if ($this->has('endereco.cep')) {
            $this->merge([
                'endereco' => array_merge($this->input('endereco', []), [
                    'cep' => preg_replace('/\D/', '', (string) $this->input('endereco.cep')),
                ]),
            ]);
        }
    }

    private function resolverIdPessoaDoMedico(?int $idMedico): ?int
    {
        if (!$idMedico) return null;
        return \App\Models\Medico::where('id', $idMedico)->value('id_pessoa');
    }
}
