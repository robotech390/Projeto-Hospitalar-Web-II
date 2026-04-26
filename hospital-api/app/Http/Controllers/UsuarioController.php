<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUsuarioRequest;
use App\Models\Usuario;
use App\Services\LogService;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Gerenciamento de usuários do sistema.
 *
 * O endpoint principal é POST /api/usuarios/registrar, chamado pelas
 * outras equipes quando criam uma entidade que precisa de acesso ao sistema
 * (ex: Grupo 2 cria um paciente → chama este endpoint para criar o login dele).
 */
class UsuarioController extends Controller
{
    // ─── Registrar usuário (chamado pelas outras equipes) ─────────────────────

    /**
     * @OA\Post(
     *     path="/usuarios/registrar",
     *     tags={"Usuários"},
     *     summary="Registrar novo usuário (endpoint para as outras equipes)",
     *     description="Cria um usuário no sistema de autenticação. Deve ser chamado quando outra equipe cria uma entidade que precisará se logar (ex: novo paciente, novo farmacêutico). Gera uma senha aleatória de primeiro acesso e a envia por e-mail ao usuário.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","funcao","id_cadastro","nome"},
     *             @OA\Property(property="email",       type="string", format="email", example="maria.santos@email.com",
     *                 description="E-mail do usuário. Será usado como login e receberá a senha de primeiro acesso."),
     *             @OA\Property(property="funcao",      type="string", example="paciente",
     *                 description="Papel do usuário. Valores aceitos: administrador | medico | farmaceutico | recepcionista | paciente"),
     *             @OA\Property(property="id_cadastro", type="integer", example=42,
     *                 description="ID do cadastro do usuário no sistema de origem (ex: ID do paciente na tabela do Grupo 2)."),
     *             @OA\Property(property="nome",        type="string", example="Maria Santos",
     *                 description="Nome completo do usuário.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso. Senha enviada por e-mail.",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem",     type="string",  example="Usuário criado com sucesso. A senha de primeiro acesso foi enviada para o e-mail informado."),
     *             @OA\Property(property="id_usuario",   type="integer", example=10),
     *             @OA\Property(property="email",        type="string",  example="maria.santos@email.com"),
     *             @OA\Property(property="funcao",       type="string",  example="paciente"),
     *             @OA\Property(property="id_cadastro",  type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token JWT ausente ou inválido")
     * )
     */
    public function registrar(RegistrarUsuarioRequest $request): JsonResponse
    {
        $senhaPrimeiroAcesso = SenhaService::gerarSenhaPrimeiroAcesso();

        $usuario = Usuario::create([
            'usuario'         => $request->nome,
            'email'           => $request->email,
            'senha'           => Hash::make($senhaPrimeiroAcesso),
            'funcao'          => $request->funcao,
            'id_cadastro'     => $request->id_cadastro,
            'primeiro_acesso' => true,
        ]);

        // Envia a senha por e-mail
        SenhaService::enviarSenhaPrimeiroAcesso(
            email:  $request->email,
            nome:   $request->nome,
            funcao: $request->funcao,
            senha:  $senhaPrimeiroAcesso,
        );

        $usuarioLogado = JWTAuth::parseToken()->authenticate();
        LogService::registrar(
            $usuarioLogado,
            "Usuário {$usuarioLogado->usuario} registrou o novo usuário '{$request->nome}' ({$request->funcao}) com e-mail {$request->email}."
        );

        return response()->json([
            'mensagem'    => 'Usuário criado com sucesso. A senha de primeiro acesso foi enviada para o e-mail informado.',
            'id_usuario'  => $usuario->id,
            'email'       => $usuario->email,
            'funcao'      => $usuario->funcao,
            'id_cadastro' => $usuario->id_cadastro,
        ], 201);
    }

    // ─── Listar usuários ──────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/usuarios",
     *     tags={"Usuários"},
     *     summary="Listar usuários",
     *     description="Retorna a lista de todos os usuários do sistema. Suporta filtro por funcao via query string.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="funcao", in="query", required=false,
     *         description="Filtra por função: administrador | medico | farmaceutico | recepcionista | paciente",
     *         @OA\Schema(type="string", example="medico")
     *     ),
     *     @OA\Response(response=200, description="Lista de usuários"),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function index(): JsonResponse
    {
        $funcao   = request('funcao');
        $usuarios = Usuario::when($funcao, fn($q) => $q->where('funcao', $funcao))
            ->select('id', 'usuario', 'email', 'funcao', 'id_cadastro', 'primeiro_acesso', 'data_criacao', 'data_alteracao')
            ->orderBy('usuario')
            ->get();

        return response()->json($usuarios);
    }

    // ─── Exibir usuário ───────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Buscar usuário por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Dados do usuário"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $usuario = Usuario::select('id', 'usuario', 'email', 'funcao', 'id_cadastro', 'primeiro_acesso', 'data_criacao', 'data_alteracao')
            ->findOrFail($id);

        return response()->json($usuario);
    }

    // ─── Atualizar usuário ────────────────────────────────────────────────────

    /**
     * @OA\Put(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Atualizar usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nome",   type="string",  example="João da Silva Atualizado"),
     *             @OA\Property(property="funcao", type="string",  example="administrador")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuário atualizado"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function update(int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);

        $dados = request()->validate([
            'nome'   => ['sometimes', 'string', 'max:100'],
            'funcao' => ['sometimes', 'string', 'in:administrador,medico,farmaceutico,recepcionista,paciente'],
        ]);

        if (isset($dados['nome'])) {
            $dados['usuario'] = $dados['nome'];
            unset($dados['nome']);
        }

        $usuario->update($dados);

        $usuarioLogado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($usuarioLogado, "Usuário {$usuarioLogado->usuario} atualizou o usuário ID {$id}.");

        return response()->json(['mensagem' => 'Usuário atualizado com sucesso.', 'usuario' => $usuario]);
    }

    // ─── Remover usuário ──────────────────────────────────────────────────────

    /**
     * @OA\Delete(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Remover usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Usuário removido"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $nomeRemovido = $usuario->usuario;
        $usuario->delete();

        $usuarioLogado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($usuarioLogado, "Usuário {$usuarioLogado->usuario} removeu o usuário '{$nomeRemovido}' (ID {$id}).");

        return response()->json(['mensagem' => 'Usuário removido com sucesso.']);
    }
}
