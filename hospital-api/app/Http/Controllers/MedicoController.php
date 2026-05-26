<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicoRequest;
use App\Models\Endereco;
use App\Models\Medico;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Services\LogService;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class MedicoController extends Controller
{
    /**
     * @OA\Get(path="/medicos", tags={"Médicos"}, summary="Listar médicos",
     *     description="Retorna médicos com dados de pessoa e endereço. Suporta filtro por status (A=Ativo, I=Inativo) e por especialidade.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="especialidade", in="query", @OA\Schema(type="string", example="Cardiologia")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", example="A", enum={"A","I"})),
     *     @OA\Response(response=200, description="Lista de médicos"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function index(): JsonResponse
    {
        $medicos = Medico::with(['pessoa.endereco'])
            ->when(request('especialidade'), fn($q) => $q->where('especialidade', 'like', '%'.request('especialidade').'%'))
            ->when(request('status'),        fn($q) => $q->where('status', request('status')))
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($m) => $this->formatar($m));

        return response()->json($medicos);
    }

    /**
     * @OA\Post(path="/medicos", tags={"Médicos"}, summary="Cadastrar médico",
     *     description="Cria Pessoa + Médico + Usuário automaticamente. Envia a senha de primeiro acesso por e-mail.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"nome","cpf","email","crm","uf_crm"},
     *             @OA\Property(property="nome",              type="string",  example="Dra. Ana Lima"),
     *             @OA\Property(property="cpf",               type="string",  example="12345678909"),
     *             @OA\Property(property="data_nascimento",   type="string",  format="date", example="1985-03-20"),
     *             @OA\Property(property="email",             type="string",  example="ana.lima@hospital.com"),
     *             @OA\Property(property="telefone",          type="string",  example="48999998888"),
     *             @OA\Property(property="crm",               type="string",  example="654321"),
     *             @OA\Property(property="uf_crm",            type="string",  example="SC"),
     *             @OA\Property(property="tipo",              type="string",  example="Especialista"),
     *             @OA\Property(property="especialidade",     type="string",  example="Pediatria"),
     *             @OA\Property(property="sub_especialidade", type="string",  example="Neonatologia"),
     *             @OA\Property(property="data_contratacao",  type="string",  format="date", example="2024-01-15"),
     *             @OA\Property(property="endereco", type="object",
     *                 @OA\Property(property="cep",        type="string", example="88700000"),
     *                 @OA\Property(property="logradouro", type="string", example="Rua das Flores"),
     *                 @OA\Property(property="numero",     type="string", example="123"),
     *                 @OA\Property(property="cidade",     type="string", example="Tubarão"),
     *                 @OA\Property(property="estado",     type="string", example="SC")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Médico cadastrado"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function store(MedicoRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $idEndereco = $this->salvarEndereco($request->input('endereco'));

            $pessoa = Pessoa::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'email'           => $request->email,
                'telefone'        => $request->telefone,
                'id_endereco'     => $idEndereco,
            ]);

            $medico = Medico::create([
                'id_pessoa'         => $pessoa->id,
                'tipo'              => $request->tipo,
                'crm'               => $request->crm,
                'uf_crm'            => $request->uf_crm,
                'especialidade'     => $request->especialidade,
                'sub_especialidade' => $request->sub_especialidade,
                'data_contratacao'  => $request->data_contratacao,
                'status'            => $request->input('status', 'A'),
            ]);

            // Cria o usuário de acesso ao sistema
            $senhaTemporaria = SenhaService::gerarSenhaPrimeiroAcesso();
            Usuario::create([
                'usuario'         => $request->nome,
                'email'           => $request->email,
                'senha'           => Hash::make($senhaTemporaria),
                'funcao'          => 'medico',
                'id_pessoa'       => $pessoa->id,
                'id_cadastro'     => $medico->id,
                'primeiro_acesso' => true,
            ]);

            SenhaService::enviarSenhaPrimeiroAcesso($request->email, $request->nome, 'medico', $senhaTemporaria);

            $logado = JWTAuth::parseToken()->authenticate();
            LogService::registrar($logado, "Usuário {$logado->usuario} cadastrou o médico '{$request->nome}' (CRM {$request->crm}-{$request->uf_crm}).");

            return response()->json([
                'mensagem' => 'Médico cadastrado com sucesso. A senha de primeiro acesso foi enviada por e-mail.',
                'medico'   => $this->formatar($medico->load('pessoa.endereco')),
            ], 201);
        });
    }

    /**
     * @OA\Get(path="/medicos/{id}", tags={"Médicos"}, summary="Buscar médico por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Dados do médico"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $medico = Medico::with('pessoa.endereco')->findOrFail($id);
        return response()->json($this->formatar($medico));
    }

    /**
     * @OA\Put(path="/medicos/{id}", tags={"Médicos"}, summary="Atualizar médico",
     *     description="Atualiza dados do médico e da pessoa associada. Sincroniza nome/e-mail no cadastro de usuário.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="nome",          type="string", example="Dra. Ana Lima Souza"),
     *         @OA\Property(property="especialidade", type="string", example="Ginecologia"),
     *         @OA\Property(property="status",        type="string", example="I", enum={"A","I"})
     *     )),
     *     @OA\Response(response=200, description="Médico atualizado"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function update(MedicoRequest $request, int $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $medico = Medico::with('pessoa.endereco')->findOrFail($id);

            // 1. Atualiza pessoa
            $medico->pessoa->update(array_filter([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'email'           => $request->email,
                'telefone'        => $request->telefone,
            ], fn($v) => !is_null($v)));

            // 2. Atualiza/cria endereço
            if ($request->filled('endereco')) {
                $dadosEndereco = $this->extrairDadosEndereco($request->input('endereco'));
                if ($medico->pessoa->id_endereco) {
                    $medico->pessoa->endereco->update($dadosEndereco);
                } else {
                    $novo = Endereco::create($dadosEndereco);
                    $medico->pessoa->update(['id_endereco' => $novo->id]);
                }
            }

            // 3. Atualiza médico
            $medico->update(array_filter([
                'tipo'              => $request->tipo,
                'crm'               => $request->crm,
                'uf_crm'            => $request->uf_crm,
                'especialidade'     => $request->especialidade,
                'sub_especialidade' => $request->sub_especialidade,
                'data_contratacao'  => $request->data_contratacao,
                'status'            => $request->status,
            ], fn($v) => !is_null($v)));

            // 4. Sincroniza dados de login do usuário associado (nome, email)
            $usuarioLogin = Usuario::where('id_pessoa', $medico->pessoa->id)->first();
            if ($usuarioLogin) {
                $usuarioLogin->update(array_filter([
                    'usuario' => $request->nome,
                    'email'   => $request->email,
                ], fn($v) => !is_null($v)));
            }

            $logado = JWTAuth::parseToken()->authenticate();
            LogService::registrar($logado, "Usuário {$logado->usuario} atualizou o médico ID {$id}.");

            return response()->json([
                'mensagem' => 'Médico atualizado com sucesso.',
                'medico'   => $this->formatar($medico->fresh()->load('pessoa.endereco')),
            ]);
        });
    }

    /**
     * @OA\Delete(path="/medicos/{id}", tags={"Médicos"}, summary="Inativar médico",
     *     description="Inativa o médico (status = I). Preserva histórico e impede novos agendamentos.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Médico inativado"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $medico = Medico::with('pessoa')->findOrFail($id);
        $medico->update(['status' => 'I']);

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($logado, "Usuário {$logado->usuario} inativou o médico '{$medico->pessoa?->nome}' (ID {$id}).");

        return response()->json(['mensagem' => 'Médico inativado com sucesso.']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function formatar(Medico $m): array
    {
        return [
            'id'                => $m->id,
            'nome'              => $m->pessoa?->nome,
            'email'             => $m->pessoa?->email,
            'cpf'               => $m->pessoa?->cpf,
            'telefone'          => $m->pessoa?->telefone,
            'data_nascimento'   => $m->pessoa?->data_nascimento?->format('Y-m-d'),
            'crm'               => $m->crm,
            'uf_crm'            => $m->uf_crm,
            'tipo'              => $m->tipo,
            'especialidade'     => $m->especialidade,
            'sub_especialidade' => $m->sub_especialidade,
            'status'            => $m->status,
            'data_contratacao'  => $m->data_contratacao?->format('Y-m-d'),
            'endereco'          => $m->pessoa?->endereco,
        ];
    }

    private function salvarEndereco(?array $dados): ?int
    {
        if (empty($dados) || empty($dados['cep'])) return null;
        return Endereco::create($this->extrairDadosEndereco($dados))->id;
    }

    private function extrairDadosEndereco(array $dados): array
    {
        return [
            'cep'        => $dados['cep']        ?? null,
            'logradouro' => $dados['logradouro'] ?? null,
            'numero'     => $dados['numero']     ?? null,
            'cidade'     => $dados['cidade']     ?? null,
            'estado'     => $dados['estado']     ?? null,
        ];
    }
}
