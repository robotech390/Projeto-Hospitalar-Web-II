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
            'data'             => 'nullable|date',
            'data_criacao'     => 'nullable|datetime',
            'data_alteracao'   => 'nullable|datetime',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fim'         => 'nullable|date_format:H:i|after:hora_inicio',
            'id_tipo_consulta' => 'nullable|integer',
            'id_paciente'      => 'nullable|integer',
            'id_medico'        => 'nullable|integer',
            'descricao'        => 'nullable|string|max:1000',
            'status'           => 'nullable|in:agendada,em_espera,em_andamento,concluida,cancelada',
            'data_check_in'    => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fim.after' => 'A hora de término deve ser posterior à hora de início.',
            'status.in'      => 'Status inválido. Use: agendada, em_espera, em_andamento, concluida ou cancelada.',
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
