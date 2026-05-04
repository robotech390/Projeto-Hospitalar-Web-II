<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

// ─────────────────────────────────────────────────────────────────────────────
// CONSULTA
// ─────────────────────────────────────────────────────────────────────────────
class ConsultaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'data'             => 'required|date',
            'hora_inicio'      => 'required|date_format:Y-m-d H:i:s',
            'hora_fim'         => 'required|date_format:Y-m-d H:i:s|after:hora_inicio',
            'id_tipo_consulta' => 'required|integer',
            'id_paciente'      => 'required|integer',
            'id_medico'        => 'required|integer',
            'descricao'        => 'nullable|string|max:1000',
            'status'           => 'nullable|in:agendada,em_espera,em_andamento,concluida,cancelada',
            'data_check_in'    => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'data.required'             => 'A data da consulta é obrigatória.',
            'data.date'                 => 'A data da consulta deve ser uma data válida.',
            'hora_inicio.required'      => 'A hora de início da consulta é obrigatória.',
            'hora_inicio.date_format'   => 'A hora de início deve estar no formato Y-m-d H:i:s.',
            'hora_fim.required'         => 'A hora de término da consulta é obrigatória.',
            'hora_fim.date_format'      => 'A hora de término deve estar no formato Y-m-d H:i:s.',
            'hora_fim.after'            => 'A hora de término deve ser posterior à hora de início.',
            'id_tipo_consulta.required' => 'O tipo da consulta é obrigatório.',
            'id_tipo_consulta.integer'  => 'O tipo da consulta deve ser um número inteiro.',
            'id_paciente.required'      => 'O paciente é obrigatório.',
            'id_paciente.integer'       => 'O paciente deve ser um número inteiro.',
            'id_medico.required'        => 'O médico é obrigatório.',
            'id_medico.integer'         => 'O médico deve ser um número inteiro.',
            'descricao.string'          => 'A descrição deve ser uma string.',
            'descricao.max'             => 'A descrição não pode exceder 1000 caracteres.',
            'status.in'                => 'Status inválido. Use agendada, em_espera, em_andamento, concluida ou cancelada.',
            'data_check_in.date_format' => 'A data de check-in deve estar no formato Y-m-d H:i:s.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// DIAGNÓSTICO
// ─────────────────────────────────────────────────────────────────────────────
class DiagnosticoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cid'      => 'required|string|max:10',
            'descricao'=> 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'cid.required'       => 'O código CID é obrigatório.',
            'descricao.required' => 'A descrição do diagnóstico é obrigatória.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// RECEITA
// ─────────────────────────────────────────────────────────────────────────────
class ReceitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'observacoes'                   => 'nullable|string|max:2000',
            'farmacia'                      => 'nullable|string|max:255',
            'data_emissao'                  => 'required|date',
            'medicamentos'                  => 'nullable|array',
            'medicamentos.*.id_medicamento' => 'required|integer',
            'medicamentos.*.posologia'      => 'nullable|string|max:500',
            'medicamentos.*.quantidade'     => 'nullable|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// MEDICAMENTO RECEITA (item avulso)
// ─────────────────────────────────────────────────────────────────────────────
class MedicamentoReceitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_medicamento' => 'required|integer',
            'posologia'      => 'nullable|string|max:500',
            'quantidade'     => 'required|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// SOLICITAÇÃO DE EXAME
// ─────────────────────────────────────────────────────────────────────────────
class SolicitacaoExameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'justificativa'         => 'nullable|string|max:1000',
            'prioridade'            => 'required|integer|in:1,2,3',
            'itens'                 => 'nullable|array',
            'itens.*.id_tipo_exame' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'prioridade.in' => 'Prioridade inválida. Use 1 (baixa), 2 (média) ou 3 (alta).',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// ITEM DE EXAME (avulso)
// ─────────────────────────────────────────────────────────────────────────────
class ItensExameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tipo_exame' => 'required|integer',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dados inválidos.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
