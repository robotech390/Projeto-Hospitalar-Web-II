<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\HistoricoLog;
use App\Models\Medico;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * MeuPerfilController
 *
 * Endpoints que o próprio usuário logado pode chamar sobre si mesmo,
 * sem privilégios de administrador.
 */
class MeuPerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/meu-perfil",
     *     tags={"Meu Perfil"},
     *     summary="Dados completos do usuário logado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dados do perfil")
     * )
     */
    public function meusDados(): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        $resposta = [
            'id'              => $usuario->id,
            'nome'            => $usuario->usuario,
            'email'           => $usuario->email,
            'funcao'          => $usuario->funcao,
            'id_cadastro'     => $usuario->id_cadastro,
            'primeiro_acesso' => $usuario->primeiro_acesso,
            'pessoa'          => null,
            'medico'          => null,
        ];

        if ($usuario->id_pessoa) {
            $pessoa = Pessoa::with('endereco')->find($usuario->id_pessoa);
            if ($pessoa) {
                $resposta['pessoa'] = [
                    'id'              => $pessoa->id,
                    'nome'            => $pessoa->nome,
                    'cpf'             => $pessoa->cpf,
                    'data_nascimento' => $pessoa->data_nascimento?->format('Y-m-d'),
                    'email'           => $pessoa->email,
                    'telefone'        => $pessoa->telefone,
                    'endereco'        => $pessoa->endereco,
                ];
            }
        }

        if ($usuario->funcao === 'medico' && $usuario->id_cadastro) {
            $medico = Medico::find($usuario->id_cadastro);
            if ($medico) {
                $resposta['medico'] = [
                    'id'                => $medico->id,
                    'crm'               => $medico->crm,
                    'uf_crm'            => $medico->uf_crm,
                    'tipo'              => $medico->tipo,
                    'especialidade'     => $medico->especialidade,
                    'sub_especialidade' => $medico->sub_especialidade,
                    'data_contratacao'  => $medico->data_contratacao?->format('Y-m-d'),
                    'status'            => $medico->status,
                ];
            }
        }

        return response()->json($resposta);
    }

    /**
     * @OA\Put(
     *     path="/meu-perfil",
     *     tags={"Meu Perfil"},
     *     summary="Atualizar dados pessoais do próprio usuário",
     *     description="Permite atualizar telefone, e-mail, data de nascimento e endereço. Dados profissionais (CRM, especialidade) só podem ser alterados por administradores.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="telefone",        type="string",  example="48999998888"),
     *         @OA\Property(property="email",           type="string",  example="novo@email.com"),
     *         @OA\Property(property="data_nascimento", type="string",  format="date"),
     *         @OA\Property(property="endereco",        type="object",
     *             @OA\Property(property="cep",        type="string"),
     *             @OA\Property(property="logradouro", type="string"),
     *             @OA\Property(property="numero",     type="string"),
     *             @OA\Property(property="cidade",     type="string"),
     *             @OA\Property(property="estado",     type="string")
     *         )
     *     )),
     *     @OA\Response(response=200, description="Perfil atualizado"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function atualizarMeusDados(Request $request): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario->id_pessoa) {
            return response()->json([
                'mensagem' => 'Seu usuário não está vinculado a uma pessoa.',
            ], 400);
        }

        // Padroniza telefone/cep antes da validação
        $request->merge(array_filter([
            'telefone' => $request->telefone ? preg_replace('/\D/', '', $request->telefone) : null,
        ]));

        if ($request->has('endereco.cep')) {
            $request->merge([
                'endereco' => array_merge($request->input('endereco', []), [
                    'cep' => preg_replace('/\D/', '', (string) $request->input('endereco.cep')),
                ]),
            ]);
        }

        $dados = $request->validate([
            'email'           => ['sometimes', 'email', 'max:100', Rule::unique('pessoa', 'email')->ignore($usuario->id_pessoa)],
            'telefone'        => ['sometimes', 'string', 'min:10', 'max:11'],
            'data_nascimento' => ['sometimes', 'date', 'before:today'],

            'endereco'            => ['sometimes', 'array'],
            'endereco.cep'        => ['sometimes', 'string', 'size:8'],
            'endereco.logradouro' => ['sometimes', 'string', 'max:100'],
            'endereco.numero'     => ['sometimes', 'string', 'max:10'],
            'endereco.cidade'     => ['sometimes', 'string', 'max:100'],
            'endereco.estado'     => ['sometimes', 'string', 'size:2'],
        ], [
            'email.email'       => 'Informe um e-mail válido.',
            'email.unique'      => 'Este e-mail já está em uso por outro usuário.',
            'telefone.min'      => 'Telefone inválido (10 ou 11 dígitos).',
            'telefone.max'      => 'Telefone inválido (10 ou 11 dígitos).',
            'endereco.cep.size' => 'O CEP deve conter 8 dígitos.',
        ]);

        $pessoa = Pessoa::with('endereco')->find($usuario->id_pessoa);

        $pessoa->update(array_filter([
            'email'           => $dados['email']           ?? null,
            'telefone'        => $dados['telefone']        ?? null,
            'data_nascimento' => $dados['data_nascimento'] ?? null,
        ], fn($v) => !is_null($v)));

        if (!empty($dados['endereco'])) {
            $dadosEndereco = [
                'cep'        => $dados['endereco']['cep']        ?? null,
                'logradouro' => $dados['endereco']['logradouro'] ?? null,
                'numero'     => $dados['endereco']['numero']     ?? null,
                'cidade'     => $dados['endereco']['cidade']     ?? null,
                'estado'     => $dados['endereco']['estado']     ?? null,
            ];

            if ($pessoa->id_endereco) {
                $pessoa->endereco->update($dadosEndereco);
            } else {
                $novo = Endereco::create($dadosEndereco);
                $pessoa->update(['id_endereco' => $novo->id]);
            }
        }

        // Sincroniza e-mail no Usuario
        if (isset($dados['email']) && $dados['email'] !== $usuario->email) {
            $emailDuplicado = Usuario::where('email', $dados['email'])
                ->where('id', '!=', $usuario->id)
                ->exists();
            if ($emailDuplicado) {
                return response()->json([
                    'mensagem' => 'Este e-mail já está em uso por outro usuário.',
                ], 422);
            }
            $usuario->update(['email' => $dados['email']]);
        }

        LogService::registrar($usuario, "Usuário {$usuario->usuario} atualizou os próprios dados pessoais.");

        return response()->json([
            'mensagem' => 'Dados atualizados com sucesso.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/meu-perfil/agenda",
     *     tags={"Meu Perfil"},
     *     summary="Listar plantões do próprio médico",
     *     description="Disponível apenas para usuários com função 'medico'.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="data_fim",    in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de plantões"),
     *     @OA\Response(response=403, description="Apenas médicos podem acessar")
     * )
     */
    public function minhaAgenda(): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        if ($usuario->funcao !== 'medico' || !$usuario->id_cadastro) {
            return response()->json([
                'mensagem' => 'Este endpoint é disponível apenas para usuários com perfil de médico.',
            ], 403);
        }

        $plantoes = \App\Models\Agenda::where('id_medico', $usuario->id_cadastro)
            ->when(request('data_inicio'), fn($q) => $q->where('data_disponibilidade', '>=', request('data_inicio')))
            ->when(request('data_fim'),    fn($q) => $q->where('data_disponibilidade', '<=', request('data_fim')))
            ->orderBy('data_disponibilidade')
            ->orderBy('hora_inicio')
            ->get()
            ->map(fn($a) => [
                'id'                   => $a->id,
                'data_disponibilidade' => $a->data_disponibilidade
                    ? (is_string($a->data_disponibilidade) ? $a->data_disponibilidade : $a->data_disponibilidade->format('Y-m-d'))
                    : null,
                'hora_inicio'          => $a->hora_inicio,
                'hora_fim'             => $a->hora_fim,
                'plantao'              => (bool) $a->plantao,
            ]);

        return response()->json($plantoes);
    }

    /**
     * @OA\Get(
     *     path="/meu-perfil/historico",
     *     tags={"Meu Perfil"},
     *     summary="Histórico de ações relacionadas ao usuário logado",
     *     description="Retorna logs em que o usuário foi o autor da ação OU em que o nome do usuário foi citado na descrição.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="limite", in="query", @OA\Schema(type="integer", example=50)),
     *     @OA\Response(response=200, description="Histórico de ações")
     * )
     */
    public function meuHistorico(): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();
        $nome    = $usuario->usuario;
        $limite  = (int) request('limite', 50);

        $logs = HistoricoLog::with(['usuario:id,usuario,email,funcao'])
            ->where(function ($q) use ($usuario, $nome) {
                $q->where('id_usuario', $usuario->id)
                  ->orWhere('descricao', 'like', "%{$nome}%");
            })
            ->orderByDesc('data')
            ->limit($limite)
            ->get();

        return response()->json($logs);
    }
}
