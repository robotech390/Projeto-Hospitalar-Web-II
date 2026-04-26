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
     *     description="Retorna médicos com dados de pessoa e endereço. Consumido pelo Grupo 2.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="especialidade", in="query", @OA\Schema(type="string", example="Cardiologia")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", example="A")),
     *     @OA\Response(response=200, description="Lista de médicos"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function index(): JsonResponse
    {
        $medicos = Medico::with(['pessoa.endereco'])
            ->when(request('especialidade'), fn($q) => $q->where('especialidade', 'like', '%'.request('especialidade').'%'))
            ->when(request('status', 'A'),   fn($q) => $q->where('status', request('status', 'A')))
            ->orderBy('id')
            ->get()
            ->map(fn($m) => [
                'id'               => $m->id,
                'nome'             => $m->pessoa?->nome,
                'email'            => $m->pessoa?->email,
                'cpf'              => $m->pessoa?->cpf,
                'telefone'         => $m->pessoa?->telefone,
                'crm'              => $m->crm,
                'uf_crm'           => $m->uf_crm,
                'tipo'             => $m->tipo,
                'especialidade'    => $m->especialidade,
                'sub_especialidade'=> $m->sub_especialidade,
                'status'           => $m->status,
                'data_contratacao' => $m->data_contratacao,
                'endereco'         => $m->pessoa?->endereco,
            ]);

        return response()->json($medicos);
    }

    /**
     * @OA\Post(path="/medicos", tags={"Médicos"}, summary="Cadastrar médico",
     *     description="Cria Pessoa + Médico + Usuário automaticamente. Envia senha de primeiro acesso por e-mail.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"nome","cpf","email","crm","uf_crm"},
     *             @OA\Property(property="nome",              type="string",  example="Dra. Ana Lima"),
     *             @OA\Property(property="cpf",               type="string",  example="12345678901"),
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
     *                 @OA\Property(property="complemento",type="string", example="Sala 2"),
     *                 @OA\Property(property="cidade",     type="string", example="Tubarão"),
     *                 @OA\Property(property="estado",     type="string", example="SC")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Médico cadastrado com sucesso"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function store(MedicoRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            // 1. Endereço (opcional)
            $idEndereco = null;
            if ($request->filled('endereco')) {
                $dadosEndereco = $request->input('endereco');
                // Garante que só campos existentes na tabela sejam enviados
                $idEndereco = Endereco::create([
                    'cep'         => $dadosEndereco['cep']         ?? null,
                    'logradouro'  => $dadosEndereco['logradouro']  ?? null,
                    'numero'      => $dadosEndereco['numero']       ?? null,
                    'complemento' => $dadosEndereco['complemento'] ?? null,
                    'cidade'      => $dadosEndereco['cidade']       ?? null,
                    'estado'      => $dadosEndereco['estado']       ?? null,
                ])->id;
            }

            // 2. Pessoa
            $pessoa = Pessoa::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'email'           => $request->email,
                'telefone'        => $request->telefone,
                'id_endereco'     => $idEndereco,
            ]);

            // 3. Médico
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

            // 4. Usuário de sistema
            $senha = SenhaService::gerarSenhaPrimeiroAcesso();
            Usuario::create([
                'usuario'         => $request->nome,
                'email'           => $request->email,
                'senha'           => Hash::make($senha),
                'funcao'          => 'medico',
                'id_pessoa'       => $pessoa->id,
                'id_cadastro'     => $medico->id,
                'primeiro_acesso' => true,
            ]);

            SenhaService::enviarSenhaPrimeiroAcesso($request->email, $request->nome, 'medico', $senha);

            $logado = JWTAuth::parseToken()->authenticate();
            LogService::registrar($logado, "Usuário {$logado->usuario} cadastrou o médico '{$request->nome}' (CRM {$request->crm}-{$request->uf_crm}).");

            return response()->json([
                'mensagem' => 'Médico cadastrado. Senha de acesso enviada por e-mail.',
                'medico'   => $medico->load('pessoa.endereco'),
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
        return response()->json(Medico::with('pessoa.endereco')->findOrFail($id));
    }

    /**
     * @OA\Put(path="/medicos/{id}", tags={"Médicos"}, summary="Atualizar médico",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="especialidade", type="string", example="Ginecologia"),
     *         @OA\Property(property="status",        type="string", example="I")
     *     )),
     *     @OA\Response(response=200, description="Médico atualizado"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function update(MedicoRequest $request, int $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $medico = Medico::with('pessoa.endereco')->findOrFail($id);

            $medico->pessoa->update(array_filter([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'email'           => $request->email,
                'telefone'        => $request->telefone,
            ], fn($v) => !is_null($v)));

            if ($request->filled('endereco')) {
                $dadosEndereco = [
                    'cep'         => $request->input('endereco.cep'),
                    'logradouro'  => $request->input('endereco.logradouro'),
                    'numero'      => $request->input('endereco.numero'),
                    'complemento' => $request->input('endereco.complemento'),
                    'cidade'      => $request->input('endereco.cidade'),
                    'estado'      => $request->input('endereco.estado'),
                ];
                if ($medico->pessoa->id_endereco) {
                    $medico->pessoa->endereco->update($dadosEndereco);
                } else {
                    $end = Endereco::create($dadosEndereco);
                    $medico->pessoa->update(['id_endereco' => $end->id]);
                }
            }

            $medico->update(array_filter([
                'tipo'              => $request->tipo,
                'crm'               => $request->crm,
                'uf_crm'            => $request->uf_crm,
                'especialidade'     => $request->especialidade,
                'sub_especialidade' => $request->sub_especialidade,
                'data_contratacao'  => $request->data_contratacao,
                'status'            => $request->status,
            ], fn($v) => !is_null($v)));

            $logado = JWTAuth::parseToken()->authenticate();
            LogService::registrar($logado, "Usuário {$logado->usuario} atualizou o médico ID {$id}.");

            return response()->json(['mensagem' => 'Médico atualizado.', 'medico' => $medico->load('pessoa.endereco')]);
        });
    }

    /**
     * @OA\Delete(path="/medicos/{id}", tags={"Médicos"}, summary="Inativar médico",
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
}
